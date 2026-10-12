<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderUser;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeProfileSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // 1. Karyawan Bulanan - Budi Santoso
        $budi = User::firstOrCreate(
            ['email' => 'budi@bunardi.com'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('password'), 'role' => 'crew']
        );
        EmployeeProfile::updateOrCreate(
            ['user_id' => $budi->id],
            [
                'nik'                 => 'EMP-001',
                'jabatan'             => 'Koordinator Lapangan & Dekorasi',
                'tipe'                => 'bulanan',
                'gaji_pokok'          => 3500000,
                'upah_per_event'      => null,
                'tunjangan_transport' => 300000,
                'tunjangan_makan'     => 200000,
                'no_hp'               => '081234567890',
                'tanggal_bergabung'   => '2024-01-15',
                'status_aktif'        => true,
            ]
        );

        // 2. Karyawan Bulanan - Siti Aminah
        $siti = User::firstOrCreate(
            ['email' => 'siti@bunardi.com'],
            ['name' => 'Siti Aminah', 'password' => Hash::make('password'), 'role' => 'crew']
        );
        EmployeeProfile::updateOrCreate(
            ['user_id' => $siti->id],
            [
                'nik'                 => 'EMP-002',
                'jabatan'             => 'Kepala Dapur & Catering',
                'tipe'                => 'bulanan',
                'gaji_pokok'          => 3200000,
                'upah_per_event'      => null,
                'tunjangan_transport' => 250000,
                'tunjangan_makan'     => 250000,
                'no_hp'               => '081298765432',
                'tanggal_bergabung'   => '2024-03-01',
                'status_aktif'        => true,
            ]
        );

        // 3. Karyawan Harian - Agus Setiawan
        $agus = User::firstOrCreate(
            ['email' => 'agus@bunardi.com'],
            ['name' => 'Agus Setiawan', 'password' => Hash::make('password'), 'role' => 'crew']
        );
        EmployeeProfile::updateOrCreate(
            ['user_id' => $agus->id],
            [
                'nik'                 => 'EMP-003',
                'jabatan'             => 'Crew Dekorasi',
                'tipe'                => 'harian',
                'gaji_pokok'          => 150000,
                'upah_per_event'      => 175000,
                'tunjangan_transport' => 0,
                'tunjangan_makan'     => 25000,
                'no_hp'               => '085712345678',
                'tanggal_bergabung'   => '2024-05-10',
                'status_aktif'        => true,
            ]
        );

        // 4. Karyawan Harian - Rian Pratama
        $rian = User::firstOrCreate(
            ['email' => 'rian@bunardi.com'],
            ['name' => 'Rian Pratama', 'password' => Hash::make('password'), 'role' => 'crew']
        );
        EmployeeProfile::updateOrCreate(
            ['user_id' => $rian->id],
            [
                'nik'                 => 'EMP-004',
                'jabatan'             => 'Crew Catering & Waiter',
                'tipe'                => 'harian',
                'gaji_pokok'          => 120000,
                'upah_per_event'      => 150000,
                'tunjangan_transport' => 0,
                'tunjangan_makan'     => 25000,
                'no_hp'               => '087812345678',
                'tanggal_bergabung'   => '2024-06-01',
                'status_aktif'        => true,
            ]
        );

        // 5. Sample Absensi untuk Budi & Siti (Bulan ini)
        $now = Carbon::now();
        for ($i = 1; $i <= min(15, $now->day); $i++) {
            $tgl = Carbon::create($now->year, $now->month, $i);
            if ($tgl->isWeekend()) {
                continue;
            }

            Attendance::firstOrCreate(
                ['user_id' => $budi->id, 'tanggal' => $tgl->toDateString()],
                ['status' => 'hadir', 'dicatat_oleh' => $admin?->id]
            );

            Attendance::firstOrCreate(
                ['user_id' => $siti->id, 'tanggal' => $tgl->toDateString()],
                ['status' => ($i === 5 ? 'izin' : 'hadir'), 'keterangan' => ($i === 5 ? 'Keperluan keluarga' : null), 'dicatat_oleh' => $admin?->id]
            );
        }

        // 6. Sample Kasbon Agus (Auto Potong Gaji)
        $loanAgus = EmployeeLoan::firstOrCreate(
            ['user_id' => $agus->id, 'jumlah' => 500000],
            [
                'sisa_hutang'       => 500000,
                'tanggal_pengajuan' => now()->subDays(10)->toDateString(),
                'tanggal_disetujui' => now()->subDays(9)->toDateString(),
                'status'            => 'disetujui',
                'metode_cicilan'    => 'auto_potong_gaji',
                'cicilan_per_bulan' => 250000,
                'disetujui_oleh'    => $admin?->id,
                'catatan'           => 'Kasbon servis motor',
            ]
        );

        // 7. Sample Kasbon Budi (Manual, sudah dicicil sebagian)
        $loanBudi = EmployeeLoan::firstOrCreate(
            ['user_id' => $budi->id, 'jumlah' => 1000000],
            [
                'sisa_hutang'       => 1000000,
                'tanggal_pengajuan' => now()->subDays(20)->toDateString(),
                'tanggal_disetujui' => now()->subDays(19)->toDateString(),
                'status'            => 'disetujui',
                'metode_cicilan'    => 'manual',
                'cicilan_per_bulan' => null,
                'disetujui_oleh'    => $admin?->id,
                'catatan'           => 'Kasbon renovasi rumah',
            ]
        );

        // Jika belum ada pembayaran untuk loan Budi, tambahkan pembayaran sebagian Rp 300.000
        if ($loanBudi->payments()->count() === 0) {
            EmployeeLoanPayment::create([
                'employee_loan_id' => $loanBudi->id,
                'jumlah_bayar'     => 300000,
                'tanggal'          => now()->subDays(5)->toDateString(),
                'sumber'           => 'manual',
                'dicatat_oleh'     => $admin?->id,
                'catatan'          => 'Cicilan 1 via transfer',
            ]);
        }

        // 8. Assign Agus & Rian ke WorkOrder aktif (jika ada) dan tandai hadir
        $wo = WorkOrder::first();
        if ($wo) {
            WorkOrderUser::updateOrCreate(
                ['work_order_id' => $wo->id, 'user_id' => $agus->id],
                ['peran' => 'Crew Dekor', 'hadir' => true, 'upah_dibayar' => 175000]
            );
            WorkOrderUser::updateOrCreate(
                ['work_order_id' => $wo->id, 'user_id' => $rian->id],
                ['peran' => 'Crew Prasmanan', 'hadir' => true, 'upah_dibayar' => 150000]
            );
        }
    }
}
