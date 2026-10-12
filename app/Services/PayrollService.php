<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\User;
use App\Models\WorkOrderUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll draft untuk bulan & tahun tertentu.
     */
    public function generate(int $bulan, int $tahun, int $dibuatOleh): Payroll
    {
        // Pastikan belum ada payroll final di bulan yang sama
        $existing = Payroll::where('bulan', $bulan)->where('tahun', $tahun)->first();
        if ($existing && $existing->status === 'final') {
            throw new \RuntimeException("Payroll {$bulan}/{$tahun} sudah final dan tidak dapat di-generate ulang.");
        }

        return DB::transaction(function () use ($bulan, $tahun, $dibuatOleh, $existing) {
            // Buat atau reset payroll header
            $payroll = $existing ?? Payroll::create([
                'bulan'      => $bulan,
                'tahun'      => $tahun,
                'status'     => 'draft',
                'dibuat_oleh' => $dibuatOleh,
            ]);

            // Hapus items lama jika re-generate
            if ($existing) {
                $existing->items()->delete();
            }

            // Tanggal range bulan ini
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();

            // Ambil semua karyawan aktif yang punya profil
            $users = User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                ->with('employeeProfile')
                ->get();

            foreach ($users as $user) {
                $profile = $user->employeeProfile;
                $item    = ['payroll_id' => $payroll->id, 'user_id' => $user->id];

                $item['tipe_karyawan']      = $profile->tipe;
                $item['gaji_pokok']         = $profile->gaji_pokok;
                $item['tunjangan_transport'] = $profile->tunjangan_transport;
                $item['tunjangan_makan']    = $profile->tunjangan_makan;

                if ($profile->tipe === 'bulanan') {
                    // Hitung hari hadir dari tabel attendances
                    $jumlahHadir = Attendance::where('user_id', $user->id)
                        ->whereBetween('tanggal', [$startDate, $endDate])
                        ->where('status', 'hadir')
                        ->count();

                    $item['jumlah_hari_hadir']  = $jumlahHadir;
                    $item['jumlah_event_hadir'] = 0;
                    $item['upah_event']         = 0;
                    $item['total_bruto'] = $profile->gaji_pokok
                        + $profile->tunjangan_transport
                        + $profile->tunjangan_makan;
                } else {
                    // Hitung event yang dihadiri (work_order_users.hadir = true)
                    // join ke work_orders untuk filter tanggal
                    $eventRows = WorkOrderUser::where('work_order_users.user_id', $user->id)
                        ->where('work_order_users.hadir', true)
                        ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
                        ->whereBetween('work_orders.tanggal_persiapan', [$startDate, $endDate])
                        ->select('work_order_users.*')
                        ->get();

                    $jumlahEvent = $eventRows->count();

                    // Upah: pakai upah_dibayar aktual jika ada, fallback ke upah_per_event profil
                    $totalUpah = $eventRows->sum(function ($row) use ($profile) {
                        return $row->upah_dibayar ?? $profile->upah_per_event ?? $profile->gaji_pokok;
                    });

                    $item['jumlah_event_hadir'] = $jumlahEvent;
                    $item['jumlah_hari_hadir']  = 0;
                    $item['upah_event']         = $totalUpah;
                    $item['total_bruto'] = $totalUpah
                        + ($profile->tunjangan_transport * $jumlahEvent)
                        + ($profile->tunjangan_makan * $jumlahEvent);
                }

                // Hitung total kasbon auto_potong aktif
                $totalKasbon = EmployeeLoan::where('user_id', $user->id)
                    ->where('status', 'disetujui')
                    ->where('metode_cicilan', 'auto_potong_gaji')
                    ->where('sisa_hutang', '>', 0)
                    ->get()
                    ->sum(function ($loan) {
                        return min($loan->cicilan_per_bulan ?? 0, $loan->sisa_hutang);
                    });

                $item['potongan_kasbon'] = $totalKasbon;
                $item['potongan_lain']   = 0;
                $item['total_bersih']    = max(0, $item['total_bruto'] - $totalKasbon);
                $item['status']          = 'draft';

                PayrollItem::create($item);
            }

            return $payroll->fresh('items');
        });
    }

    /**
     * Finalisasi payroll: kunci, proses auto-potong kasbon.
     */
    public function finalize(Payroll $payroll): void
    {
        if ($payroll->status === 'final') {
            throw new \RuntimeException('Payroll sudah final.');
        }

        DB::transaction(function () use ($payroll) {
            foreach ($payroll->items as $item) {
                if ($item->potongan_kasbon <= 0) {
                    continue;
                }

                // Ambil kasbon aktif auto_potong milik karyawan ini
                $loans = EmployeeLoan::where('user_id', $item->user_id)
                    ->where('status', 'disetujui')
                    ->where('metode_cicilan', 'auto_potong_gaji')
                    ->where('sisa_hutang', '>', 0)
                    ->get();

                foreach ($loans as $loan) {
                    $potong = min($loan->cicilan_per_bulan ?? 0, $loan->sisa_hutang);
                    if ($potong <= 0) {
                        continue;
                    }

                    EmployeeLoanPayment::create([
                        'employee_loan_id' => $loan->id,
                        'jumlah_bayar'     => $potong,
                        'tanggal'          => now()->toDateString(),
                        'sumber'           => 'potong_gaji',
                        'payroll_item_id'  => $item->id,
                        'dicatat_oleh'     => $payroll->dibuat_oleh,
                        'catatan'          => "Auto potong gaji {$payroll->nama_bulan}",
                    ]);
                    // sisa_hutang diupdate otomatis oleh EmployeeLoanPayment::booted()
                }
            }

            $payroll->update([
                'status'         => 'final',
                'tanggal_proses' => now()->toDateString(),
            ]);
        });
    }
}
