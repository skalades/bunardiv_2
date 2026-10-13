<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EmployeeProfile;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfSettingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Customer $customer;
    protected Order $order;
    protected Payment $payment;
    protected WorkOrder $workOrder;
    protected PayrollItem $payrollItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Keuangan']);

        $this->customer = Customer::create([
            'nama'    => 'Budi Prasetyo',
            'telepon' => '08123456789',
            'alamat'  => 'Jl. Anggrek No. 10',
        ]);

        $this->order = Order::create([
            'customer_id' => $this->customer->id,
            'no_order'    => 'ORD-SETTING-001',
            'nama_acara'  => 'Resepsi Budi & Siti',
            'tanggal'     => '2026-10-25',
            'status'      => 'proses',
            'total'       => 35000000,
        ]);

        $this->payment = Payment::create([
            'order_id'         => $this->order->id,
            'amount'           => 15000000,
            'payment_date'     => '2026-10-20',
            'payment_method'   => 'Transfer BCA',
            'reference_number' => 'PAY-SETTING-001',
        ]);

        $this->workOrder = WorkOrder::create([
            'order_id'           => $this->order->id,
            'spk_number'         => 'SPK-2026-001',
            'surat_jalan_number' => 'SJ-2026-001',
            'pic_id'             => $this->admin->id,
            'tanggal_persiapan'  => '2026-10-24',
            'status'             => 'draft',
        ]);

        $payroll = Payroll::create([
            'tipe_periode'   => 'bulanan',
            'bulan'          => 10,
            'tahun'          => 2026,
            'tanggal_proses' => '2026-10-31',
            'status'         => 'final',
            'dibuat_oleh'    => $this->admin->id,
        ]);

        $crewUser = User::factory()->create(['role' => 'crew', 'name' => 'Doni Operator']);
        EmployeeProfile::create([
            'user_id' => $crewUser->id,
            'nik'     => 'EMP-001',
            'posisi'  => 'Sound & Lighting Crew',
        ]);

        $this->payrollItem = PayrollItem::create([
            'payroll_id'          => $payroll->id,
            'user_id'             => $crewUser->id,
            'tipe_karyawan'       => 'bulanan',
            'gaji_pokok'          => 3000000,
            'tunjangan_transport' => 300000,
            'tunjangan_makan'     => 200000,
            'bonus'               => 200000,
            'total_bruto'         => 3700000,
            'total_bersih'        => 3700000,
            'metode_bayar'        => 'Transfer Bank',
            'status'              => 'paid',
        ]);
    }

    public function test_all_pdfs_render_with_custom_setting_data(): void
    {
        Setting::create([
            'company_name'    => 'PT BUNARDI MEGAH NUSANTARA',
            'company_address' => 'Gedung Kencana Lt. 5, Jl. Rasuna Said Kav. 88, Jakarta Selatan',
            'company_phone'   => '021-5558899',
            'company_email'   => 'finance@megahnusantara.co.id',
            'company_logo'    => null,
            'bank_accounts'   => [
                [
                    'bank_name'      => 'BCA Prioritas',
                    'account_number' => '888-777-6655',
                    'account_name'   => 'PT Bunardi Megah Nusantara',
                ],
                [
                    'bank_name'      => 'Bank BNI',
                    'account_number' => '099-8877-665',
                    'account_name'   => 'PT Bunardi Megah Nusantara',
                ],
            ],
            'invoice_notes'   => 'Catatan Khusus: Harap lampirkan bukti transfer saat konfirmasi pembayaran.',
        ]);

        // 1. Invoice PDF
        $resInvoice = $this->actingAs($this->admin)->get(route('invoice.download', $this->order));
        $resInvoice->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resInvoice->headers->get('content-type'));

        // 2. Receipt PDF (Kwitansi)
        $resReceipt = $this->actingAs($this->admin)->get(route('receipt.download', $this->payment));
        $resReceipt->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resReceipt->headers->get('content-type'));

        // 3. SPK PDF
        $resSpk = $this->actingAs($this->admin)->get(route('spk.download', $this->workOrder));
        $resSpk->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSpk->headers->get('content-type'));

        // 4. Surat Jalan PDF
        $resSj = $this->actingAs($this->admin)->get(route('surat_jalan.download', $this->workOrder));
        $resSj->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSj->headers->get('content-type'));

        // 5. Slip Gaji PDF
        $resSlip = $this->actingAs($this->admin)->get(route('payroll.slip', $this->payrollItem));
        $resSlip->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSlip->headers->get('content-type'));

        // 6. Laporan Keuangan PDF
        $resLap = $this->actingAs($this->admin)->get(route('laporan_keuangan.pdf', [
            'year'     => 2026,
            'month'    => 'all',
            'viewMode' => 'kas',
        ]));
        $resLap->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resLap->headers->get('content-type'));

        // 7. Dynamic Invoice View compilation
        $setting = Setting::getSettings();
        $htmlDynamic = view('pdf.invoice_dynamic', [
            'order'           => $this->order,
            'setting'         => $setting,
            'judul_tagihan'   => 'Tagihan Termin 1 (DP 50%)',
            'nominal_tagihan' => 17500000,
        ])->render();

        $this->assertStringContainsString('PT BUNARDI MEGAH NUSANTARA', $htmlDynamic);
        $this->assertStringContainsString('888-777-6655', $htmlDynamic);
        $this->assertStringContainsString('BCA Prioritas', $htmlDynamic);
        $this->assertStringContainsString('Gedung Kencana Lt. 5', $htmlDynamic);
    }

    public function test_all_pdfs_render_safely_when_setting_is_empty(): void
    {
        // Ensure no setting in database
        Setting::truncate();

        // 1. Invoice PDF
        $resInvoice = $this->actingAs($this->admin)->get(route('invoice.download', $this->order));
        $resInvoice->assertSuccessful();

        // 2. Receipt PDF
        $resReceipt = $this->actingAs($this->admin)->get(route('receipt.download', $this->payment));
        $resReceipt->assertSuccessful();

        // 3. SPK PDF
        $resSpk = $this->actingAs($this->admin)->get(route('spk.download', $this->workOrder));
        $resSpk->assertSuccessful();

        // 4. Surat Jalan PDF
        $resSj = $this->actingAs($this->admin)->get(route('surat_jalan.download', $this->workOrder));
        $resSj->assertSuccessful();

        // 5. Slip Gaji PDF
        $resSlip = $this->actingAs($this->admin)->get(route('payroll.slip', $this->payrollItem));
        $resSlip->assertSuccessful();

        // 6. Laporan Keuangan PDF
        $resLap = $this->actingAs($this->admin)->get(route('laporan_keuangan.pdf', [
            'year'     => 2026,
            'month'    => '10',
            'viewMode' => 'akrual',
        ]));
        $resLap->assertSuccessful();

        // 7. Dynamic Invoice
        $htmlDynamic = view('pdf.invoice_dynamic', [
            'order'           => $this->order,
            'setting'         => Setting::getSettings(),
            'judul_tagihan'   => 'Tagihan Pelunasan',
            'nominal_tagihan' => 20000000,
        ])->render();

        $this->assertNotEmpty($htmlDynamic);
    }
}
