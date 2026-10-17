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
     * Generate payroll harian untuk tanggal tertentu (Sangat simpel & cocok untuk crew per hari/event).
     */
    public function generateDaily(string|Carbon $tanggal, int $dibuatOleh, bool $onlyAttended = true): Payroll
    {
        $date = Carbon::parse($tanggal)->toDateString();
        $carbon = Carbon::parse($date);

        $existing = Payroll::where('tipe_periode', 'harian')->whereDate('tanggal', $date)->first();
        if ($existing && $existing->status === 'final') {
            throw new \RuntimeException('Payroll harian tanggal '.$carbon->translatedFormat('d F Y').' sudah final dan tidak dapat di-generate ulang.');
        }

        return DB::transaction(function () use ($date, $carbon, $dibuatOleh, $existing, $onlyAttended) {
            $payroll = $existing ?? Payroll::create([
                'tipe_periode' => 'harian',
                'tanggal' => $date,
                'bulan' => $carbon->month,
                'tahun' => $carbon->year,
                'status' => 'draft',
                'dibuat_oleh' => $dibuatOleh,
            ]);

            if ($existing) {
                $existing->items()->delete();
            }

            $users = User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                ->with('employeeProfile')
                ->get();

            foreach ($users as $user) {
                $profile = $user->employeeProfile;

                // Ambil penugasan SPK yang hadir pada tanggal ini
                $eventRows = WorkOrderUser::where('work_order_users.user_id', $user->id)
                    ->where('work_order_users.hadir', true)
                    ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
                    ->whereDate('work_orders.tanggal_persiapan', $date)
                    ->select('work_order_users.*')
                    ->get();

                $jumlahEvent = $eventRows->count();

                // Cek presensi kantor harian pada tanggal ini
                $hadirKantor = Attendance::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->where('status', 'hadir')
                    ->exists();

                $jumlahHari = $hadirKantor ? 1 : 0;

                // Jika filter hanya karyawan yang hadir/bertugas aktif, dan tidak hadir sama sekali hari ini, lewati
                if ($onlyAttended && $jumlahEvent === 0 && ! $hadirKantor) {
                    continue;
                }

                $attendanceHariIni = Attendance::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->first();
                $dendaHariIni = (float) ($attendanceHariIni?->denda ?? 0);

                $item = [
                    'payroll_id' => $payroll->id,
                    'user_id' => $user->id,
                    'tipe_karyawan' => $profile->tipe,
                    'jumlah_hari_hadir' => $jumlahHari,
                    'jumlah_event_hadir' => $jumlahEvent,
                    'bonus' => 0,
                    'lembur' => 0,
                    'potongan_kasbon' => 0,
                    'potongan_lain' => $dendaHariIni,
                    'status' => 'draft',
                ];

                if ($profile->tipe === 'harian') {
                    $totalUpah = $eventRows->sum(function ($row) use ($profile) {
                        return $row->upah_dibayar ?? $profile->upah_per_event ?? $profile->gaji_pokok;
                    });

                    // Jika tidak ada SPK tapi ada presensi kantor hari ini
                    if ($jumlahEvent === 0 && $hadirKantor) {
                        $totalUpah = $profile->gaji_pokok;
                    }

                    $item['gaji_pokok'] = $profile->gaji_pokok;
                    $item['upah_event'] = $totalUpah;
                    $item['tunjangan_transport'] = $profile->tunjangan_transport * max(1, $jumlahEvent);
                    $item['tunjangan_makan'] = $profile->tunjangan_makan * max(1, $jumlahEvent);
                    $item['total_bruto'] = $totalUpah + $item['tunjangan_transport'] + $item['tunjangan_makan'];
                } else {
                    // Karyawan Bulanan: upah harian pro-rata (gaji pokok / 25 hari kerja)
                    $gajiHarian = round($profile->gaji_pokok / 25);
                    $item['gaji_pokok'] = $gajiHarian;
                    $item['upah_event'] = 0;
                    $item['tunjangan_transport'] = $profile->tunjangan_transport;
                    $item['tunjangan_makan'] = $profile->tunjangan_makan;
                    $item['total_bruto'] = ($gajiHarian * $jumlahHari)
                        + ($profile->tunjangan_transport * $jumlahHari)
                        + ($profile->tunjangan_makan * $jumlahHari);
                }

                $item['total_bersih'] = max(0, $item['total_bruto'] - $item['potongan_kasbon'] - $item['potongan_lain']);

                PayrollItem::create($item);
            }

            return $payroll->fresh('items');
        });
    }

    /**
     * Generate payroll bulanan untuk bulan & tahun tertentu (Rekap sebulan penuh).
     */
    public function generate(int $bulan, int $tahun, int $dibuatOleh): Payroll
    {
        // Pastikan belum ada payroll bulanan final di bulan yang sama
        $existing = Payroll::where('tipe_periode', 'bulanan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($existing && $existing->status === 'final') {
            throw new \RuntimeException("Payroll bulanan {$bulan}/{$tahun} sudah final dan tidak dapat di-generate ulang.");
        }

        return DB::transaction(function () use ($bulan, $tahun, $dibuatOleh, $existing) {
            $payroll = $existing ?? Payroll::create([
                'tipe_periode' => 'bulanan',
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => 'draft',
                'dibuat_oleh' => $dibuatOleh,
            ]);

            if ($existing) {
                $existing->items()->delete();
            }

            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $users = User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                ->with('employeeProfile')
                ->get();

            foreach ($users as $user) {
                $profile = $user->employeeProfile;
                $item = ['payroll_id' => $payroll->id, 'user_id' => $user->id];

                $item['tipe_karyawan'] = $profile->tipe;
                $item['gaji_pokok'] = $profile->gaji_pokok;
                $item['bonus'] = 0;
                $item['lembur'] = 0;
                $item['tunjangan_transport'] = $profile->tunjangan_transport;
                $item['tunjangan_makan'] = $profile->tunjangan_makan;

                if ($profile->tipe === 'bulanan') {
                    $jumlahHadir = Attendance::where('user_id', $user->id)
                        ->whereDate('tanggal', '>=', $startDate->toDateString())
                        ->whereDate('tanggal', '<=', $endDate->toDateString())
                        ->where('status', 'hadir')
                        ->count();

                    $item['jumlah_hari_hadir'] = $jumlahHadir;
                    $item['jumlah_event_hadir'] = 0;
                    $item['upah_event'] = 0;
                    $item['total_bruto'] = $profile->gaji_pokok
                        + $profile->tunjangan_transport
                        + $profile->tunjangan_makan;
                } else {
                    $eventRows = WorkOrderUser::where('work_order_users.user_id', $user->id)
                        ->where('work_order_users.hadir', true)
                        ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
                        ->whereBetween('work_orders.tanggal_persiapan', [$startDate, $endDate])
                        ->select('work_order_users.*')
                        ->get();

                    $jumlahEvent = $eventRows->count();

                    $totalUpah = $eventRows->sum(function ($row) use ($profile) {
                        return $row->upah_dibayar ?? $profile->upah_per_event ?? $profile->gaji_pokok;
                    });

                    $item['jumlah_event_hadir'] = $jumlahEvent;
                    $item['jumlah_hari_hadir'] = 0;
                    $item['upah_event'] = $totalUpah;
                    $item['total_bruto'] = $totalUpah
                        + ($profile->tunjangan_transport * $jumlahEvent)
                        + ($profile->tunjangan_makan * $jumlahEvent);
                }

                // Hitung total kasbon auto_potong aktif (hanya pada payroll bulanan)
                $totalKasbon = EmployeeLoan::where('user_id', $user->id)
                    ->where('status', 'disetujui')
                    ->where('metode_cicilan', 'auto_potong_gaji')
                    ->where('sisa_hutang', '>', 0)
                    ->get()
                    ->sum(function ($loan) {
                        return min($loan->cicilan_per_bulan ?? 0, $loan->sisa_hutang);
                    });

                // Hitung total denda keterlambatan/absensi pada bulan ini
                $totalDenda = (float) Attendance::where('user_id', $user->id)
                    ->whereDate('tanggal', '>=', $startDate->toDateString())
                    ->whereDate('tanggal', '<=', $endDate->toDateString())
                    ->sum('denda');

                $item['potongan_kasbon'] = $totalKasbon;
                $item['potongan_lain'] = $totalDenda;
                $item['total_bersih'] = max(0, $item['total_bruto'] - $totalKasbon - $totalDenda);
                $item['status'] = 'draft';

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
                        'jumlah_bayar' => $potong,
                        'tanggal' => now()->toDateString(),
                        'sumber' => 'potong_gaji',
                        'payroll_item_id' => $item->id,
                        'dicatat_oleh' => $payroll->dibuat_oleh,
                        'catatan' => "Auto potong gaji {$payroll->nama_bulan}",
                    ]);
                }
            }

            $payroll->update([
                'status' => 'final',
                'tanggal_proses' => now()->toDateString(),
            ]);
        });
    }
}
