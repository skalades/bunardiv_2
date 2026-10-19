<?php

namespace Tests\Feature;

use App\Filament\Pages\AbsensiSaya;
use App\Filament\Resources\AbsensiResource\Pages\CreateAbsensi;
use App\Filament\Resources\AbsensiResource\Pages\EditAbsensi;
use App\Filament\Resources\AbsensiResource\Pages\ListAbsensis;
use App\Models\Attendance;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManualAttendanceWithJamKerjaAndDendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_manual_attendance_with_jam_kerja_and_custom_denda(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['name' => 'Budi Santoso']);
        EmployeeProfile::create([
            'user_id' => $employee->id,
            'jabatan' => 'Staff Kantor',
            'tipe' => 'bulanan',
            'status_aktif' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateAbsensi::class)
            ->fillForm([
                'user_id' => $employee->id,
                'tanggal' => '2026-10-17',
                'status' => 'hadir',
                'jam_masuk' => '08:45',
                'jam_pulang' => '17:15',
                'denda' => 10000,
                'keterangan' => 'Terlambat 45 menit karena macet',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $attendance = Attendance::where('user_id', $employee->id)
            ->whereDate('tanggal', '2026-10-17')
            ->first();

        $this->assertNotNull($attendance);
        $this->assertEquals('hadir', $attendance->status);
        $this->assertEquals('08:45', substr($attendance->jam_masuk, 0, 5));
        $this->assertEquals('17:15', substr($attendance->jam_pulang, 0, 5));
        $this->assertEquals(10000, (float) $attendance->denda);
        $this->assertEquals('08:45 - 17:15', $attendance->jam_kerja);
        $this->assertEquals($admin->id, $attendance->dicatat_oleh);
    }

    public function test_admin_can_edit_attendance_and_adjust_denda(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['name' => 'Siti Aminah']);
        EmployeeProfile::create([
            'user_id' => $employee->id,
            'jabatan' => 'Admin Shift',
            'tipe' => 'bulanan',
            'status_aktif' => true,
        ]);

        $attendance = Attendance::create([
            'user_id' => $employee->id,
            'tanggal' => '2026-10-17',
            'status' => 'hadir',
            'jam_masuk' => '08:30',
            'jam_pulang' => '17:00',
            'denda' => 5000,
            'dicatat_oleh' => $admin->id,
        ]);

        $this->actingAs($admin);

        // Admin adjusts denda to 0 (misal dispensasi) and updates jam pulang
        Livewire::test(EditAbsensi::class, ['record' => $attendance->getRouteKey()])
            ->fillForm([
                'jam_pulang' => '17:45',
                'denda' => 0,
                'keterangan' => 'Denda dihapus karena ada dispensasi dinas luar',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $attendance->refresh();
        $this->assertEquals('17:45', substr($attendance->jam_pulang, 0, 5));
        $this->assertEquals(0, (float) $attendance->denda);
        $this->assertStringContainsString('dispensasi', $attendance->keterangan);
    }

    public function test_bulk_attendance_records_jam_kerja_and_denda(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        EmployeeProfile::create(['user_id' => $user1->id, 'status_aktif' => true, 'tipe' => 'bulanan']);
        EmployeeProfile::create(['user_id' => $user2->id, 'status_aktif' => true, 'tipe' => 'harian']);

        $this->actingAs($admin);

        Livewire::test(ListAbsensis::class)
            ->callAction('bulk_attendance', [
                'tanggal' => '2026-10-18',
                'status' => 'hadir',
                'jam_masuk' => '06:00',
                'jam_pulang' => '15:00',
                'denda' => 5000,
                'user_ids' => [$user1->id, $user2->id],
                'keterangan' => 'Shift 1 pagi serentak',
            ])
            ->assertHasNoErrors();

        $att1 = Attendance::where('user_id', $user1->id)->whereDate('tanggal', '2026-10-18')->first();
        $att2 = Attendance::where('user_id', $user2->id)->whereDate('tanggal', '2026-10-18')->first();

        $this->assertNotNull($att1);
        $this->assertNotNull($att2);
        $this->assertEquals('06:00', substr($att1->jam_masuk, 0, 5));
        $this->assertEquals('15:00', substr($att1->jam_pulang, 0, 5));
        $this->assertEquals(5000, (float) $att1->denda);
        $this->assertEquals(5000, (float) $att2->denda);
    }

    public function test_helper_hitung_denda_telat_calculates_correctly(): void
    {
        // Tepat waktu atau lebih awal -> denda 0
        $this->assertEquals(0, Attendance::hitungDendaTelat('07:55', '08:00'));
        $this->assertEquals(0, Attendance::hitungDendaTelat('08:00', '08:00'));

        // Telat 15 menit (1 blok 30 mnt) -> Rp 5.000
        $this->assertEquals(5000, Attendance::hitungDendaTelat('08:15', '08:00'));

        // Telat 30 menit (1 blok 30 mnt) -> Rp 5.000
        $this->assertEquals(5000, Attendance::hitungDendaTelat('08:30', '08:00'));

        // Telat 31 menit (2 blok 30 mnt) -> Rp 10.000
        $this->assertEquals(10000, Attendance::hitungDendaTelat('08:31', '08:00'));

        // Telat 60 menit (2 blok 30 mnt) -> Rp 10.000
        $this->assertEquals(10000, Attendance::hitungDendaTelat('09:00', '08:00'));

        // Shift beda, misal shift 06:00, masuk 07:10 (telat 70 mnt -> 3 blok) -> Rp 15.000
        $this->assertEquals(15000, Attendance::hitungDendaTelat('07:10', '06:00'));
    }

    public function test_payroll_service_deducts_attendance_denda_automatically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create();
        EmployeeProfile::create([
            'user_id' => $employee->id,
            'gaji_pokok' => 2500000,
            'tunjangan_transport' => 100000,
            'tunjangan_makan' => 100000,
            'tipe' => 'bulanan',
            'status_aktif' => true,
        ]);

        // Buat 2 absensi dengan denda
        Attendance::create([
            'user_id' => $employee->id,
            'tanggal' => '2026-10-05',
            'status' => 'hadir',
            'jam_masuk' => '08:35',
            'jam_pulang' => '17:00',
            'denda' => 10000,
            'dicatat_oleh' => $admin->id,
        ]);

        Attendance::create([
            'user_id' => $employee->id,
            'tanggal' => '2026-10-06',
            'status' => 'hadir',
            'jam_masuk' => '08:15',
            'jam_pulang' => '17:00',
            'denda' => 5000,
            'dicatat_oleh' => $admin->id,
        ]);

        $service = app(PayrollService::class);
        $payroll = $service->generate(10, 2026, $admin->id);

        $item = $payroll->items()->where('user_id', $employee->id)->first();
        $this->assertNotNull($item);

        // Total denda 10.000 + 5.000 = 15.000 harus masuk ke potongan_lain
        $this->assertEquals(15000, (float) $item->potongan_lain);
        $this->assertEquals(2700000, (float) $item->total_bruto); // 2.5jt + 100k + 100k
        $this->assertEquals(2685000, (float) $item->total_bersih); // 2.7jt - 15k denda
    }

    public function test_employee_can_absen_pulang_on_portal(): void
    {
        $user = User::factory()->create();
        EmployeeProfile::create([
            'user_id' => $user->id,
            'tipe' => 'bulanan',
            'status_aktif' => true,
        ]);

        $this->actingAs($user);

        // Clock in
        Livewire::test(AbsensiSaya::class)
            ->call('checkInDaily', 'hadir')
            ->assertHasNoErrors();

        $att = Attendance::where('user_id', $user->id)->whereDate('tanggal', now()->toDateString())->first();
        $this->assertNotNull($att);
        $this->assertNotNull($att->jam_masuk);
        $this->assertNull($att->jam_pulang);

        // Clock out
        Livewire::test(AbsensiSaya::class)
            ->call('checkOutDaily')
            ->assertHasNoErrors();

        $att->refresh();
        $this->assertNotNull($att->jam_pulang);
    }

    public function test_admin_can_create_attendance_with_null_or_empty_denda_without_exception(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['name' => 'Karyawan Tanpa Denda']);
        EmployeeProfile::create([
            'user_id' => $employee->id,
            'jabatan' => 'Staff Kantor',
            'tipe' => 'bulanan',
            'status_aktif' => true,
        ]);

        $this->actingAs($admin);

        // Simulasi form disubmit dengan denda kosong / null seperti pada gambar error
        Livewire::test(CreateAbsensi::class)
            ->fillForm([
                'user_id' => $employee->id,
                'tanggal' => '2026-10-19',
                'status' => 'hadir',
                'jam_masuk' => '07:03',
                'jam_pulang' => '18:00',
                'denda' => null,
                'keterangan' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $att = Attendance::where('user_id', $employee->id)->whereDate('tanggal', '2026-10-19')->first();
        $this->assertNotNull($att);
        $this->assertEquals(0, (float) $att->denda);
        $this->assertEquals('07:03', substr($att->jam_masuk, 0, 5));
        $this->assertEquals('18:00', substr($att->jam_pulang, 0, 5));
    }

    public function test_attendance_model_mutator_defaults_null_denda_to_zero(): void
    {
        $user = User::factory()->create();

        $att = Attendance::create([
            'user_id' => $user->id,
            'tanggal' => '2026-10-20',
            'status' => 'hadir',
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
            'denda' => null, // explicitly null
        ]);

        $this->assertEquals(0, (float) $att->denda);

        $att->update(['denda' => '']);
        $this->assertEquals(0, (float) $att->denda);
    }
}

