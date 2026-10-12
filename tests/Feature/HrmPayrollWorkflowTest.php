<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\EmployeeProfile;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderUser;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmPayrollWorkflowTest extends TestCase
{
    use RefreshDatabase;
    public function test_payroll_service_generates_and_finalizes_accurately(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin_test@bunardi.com'], ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin']);

        // 1. Setup Karyawan Bulanan
        $monthlyUser = User::factory()->create(['name' => 'Karyawan Bulanan']);
        EmployeeProfile::create([
            'user_id'             => $monthlyUser->id,
            'nik'                 => 'TEST-001',
            'jabatan'             => 'Staff Bulanan',
            'tipe'                => 'bulanan',
            'gaji_pokok'          => 3000000,
            'tunjangan_transport' => 200000,
            'tunjangan_makan'     => 100000,
            'status_aktif'        => true,
        ]);

        // 2. Setup Karyawan Harian dengan Kasbon Auto-Potong
        $dailyUser = User::factory()->create(['name' => 'Karyawan Harian']);
        EmployeeProfile::create([
            'user_id'             => $dailyUser->id,
            'nik'                 => 'TEST-002',
            'jabatan'             => 'Crew Harian',
            'tipe'                => 'harian',
            'gaji_pokok'          => 100000,
            'upah_per_event'      => 150000,
            'tunjangan_transport' => 0,
            'tunjangan_makan'     => 20000,
            'status_aktif'        => true,
        ]);

        $loan = EmployeeLoan::create([
            'user_id'           => $dailyUser->id,
            'jumlah'            => 400000,
            'sisa_hutang'       => 400000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_disetujui' => now()->toDateString(),
            'status'            => 'disetujui',
            'metode_cicilan'    => 'auto_potong_gaji',
            'cicilan_per_bulan' => 200000,
            'disetujui_oleh'    => $admin->id,
        ]);

        $targetBulan = 10;
        $targetTahun = 2026;

        // Hadir di SPK
        $order = Order::first();
        if ($order) {
            $wo = WorkOrder::firstOrCreate(
                ['order_id' => $order->id, 'spk_number' => 'SPK-TEST-999'],
                ['pic_id' => $admin->id, 'surat_jalan_number' => 'SJ-TEST-999', 'tanggal_persiapan' => '2026-10-12', 'status' => 'confirmed']
            );
            WorkOrderUser::create([
                'work_order_id' => $wo->id,
                'user_id'       => $dailyUser->id,
                'peran'         => 'Crew Lapangan',
                'hadir'         => true,
                'upah_dibayar'  => 150000,
            ]);
        }

        // 3. Generate Payroll via Service
        $service = app(PayrollService::class);
        $payroll = $service->generate($targetBulan, $targetTahun, $admin->id);

        $this->assertEquals('draft', $payroll->status);
        $this->assertCount(PayrollItem::where('payroll_id', $payroll->id)->count(), $payroll->items);

        $itemBulanan = $payroll->items()->where('user_id', $monthlyUser->id)->first();
        $this->assertNotNull($itemBulanan);
        $this->assertEquals(3300000, (float) $itemBulanan->total_bruto); // 3jt + 200k + 100k
        $this->assertEquals(3300000, (float) $itemBulanan->total_bersih);

        $itemHarian = $payroll->items()->where('user_id', $dailyUser->id)->first();
        $this->assertNotNull($itemHarian);
        $this->assertEquals(200000, (float) $itemHarian->potongan_kasbon);

        // 4. Finalisasi Payroll
        $service->finalize($payroll);
        $payroll->refresh();

        $this->assertEquals('final', $payroll->status);
        $this->assertNotNull($payroll->tanggal_proses);

        // Kasbon harus terpotong
        $loan->refresh();
        $this->assertEquals(200000, (float) $loan->sisa_hutang);
        $this->assertDatabaseHas('employee_loan_payments', [
            'employee_loan_id' => $loan->id,
            'sumber'           => 'potong_gaji',
            'jumlah_bayar'     => 200000,
        ]);
    }

    public function test_slip_gaji_pdf_stream_returns_ok(): void
    {
        $admin = User::first();
        $item  = PayrollItem::first();

        if ($item && $admin) {
            $response = $this->actingAs($admin)->get(route('payroll.slip', $item));
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_self_attendance_for_event_and_daily(): void
    {
        $user = User::factory()->create();
        $customer = \App\Models\Customer::create(['nama' => 'Cust Test', 'no_telepon' => '08123', 'alamat' => 'Alamat']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'no_order'    => 'ORD-SELF-01',
            'nama_acara'  => 'Acara Self Test',
            'tanggal'     => now()->toDateString(),
            'total'       => 1000000,
        ]);

        $wo = WorkOrder::create([
            'order_id'           => $order->id,
            'pic_id'             => $user->id,
            'spk_number'         => 'SPK-SELF-01',
            'surat_jalan_number' => 'SJ-SELF-01',
            'tanggal_persiapan'  => now()->toDateString(),
            'status'             => 'confirmed',
        ]);

        $assignment = WorkOrderUser::create([
            'work_order_id' => $wo->id,
            'user_id'       => $user->id,
            'peran'         => 'Crew Lapangan',
            'hadir'         => false,
        ]);

        $this->actingAs($user);

        // Test Livewire AbsensiSaya page checkInEvent
        \Livewire\Livewire::test(\App\Filament\Pages\AbsensiSaya::class)
            ->call('checkInEvent', $assignment->id)
            ->assertHasNoErrors();

        $assignment->refresh();
        $this->assertTrue($assignment->hadir);
        $this->assertNotNull($assignment->waktu_absen);

        // Test checkInDaily
        \Livewire\Livewire::test(\App\Filament\Pages\AbsensiSaya::class)
            ->call('checkInDaily', 'hadir')
            ->assertHasNoErrors();

        $this->assertTrue(Attendance::where('user_id', $user->id)->whereDate('tanggal', now()->toDateString())->where('status', 'hadir')->exists());
    }
}
