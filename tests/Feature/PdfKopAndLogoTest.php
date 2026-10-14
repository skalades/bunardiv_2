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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfKopAndLogoTest extends TestCase
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
            'nama' => 'Budi Prasetyo',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Patrakomala No. 10',
        ]);

        $this->order = Order::create([
            'customer_id' => $this->customer->id,
            'no_order' => 'ORD-KOP-001',
            'nama_acara' => 'Resepsi Pernikahan',
            'tanggal' => '2026-10-25',
            'status' => 'proses',
            'total' => 25000000,
        ]);

        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'amount' => 10000000,
            'payment_date' => '2026-10-20',
            'payment_method' => 'Transfer BCA',
            'reference_number' => 'PAY-KOP-001',
        ]);

        $this->workOrder = WorkOrder::create([
            'order_id' => $this->order->id,
            'spk_number' => 'SPK-KOP-001',
            'surat_jalan_number' => 'SJ-KOP-001',
            'pic_id' => $this->admin->id,
            'tanggal_persiapan' => '2026-10-24',
            'status' => 'draft',
        ]);

        $payroll = Payroll::create([
            'tipe_periode' => 'bulanan',
            'bulan' => 10,
            'tahun' => 2026,
            'tanggal_proses' => '2026-10-31',
            'status' => 'final',
            'dibuat_oleh' => $this->admin->id,
        ]);

        $crewUser = User::factory()->create(['role' => 'crew', 'name' => 'Karyawan Katering']);
        EmployeeProfile::create([
            'user_id' => $crewUser->id,
            'nik' => 'EMP-KOP-01',
            'posisi' => 'Staff Lapangan',
        ]);

        $this->payrollItem = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'user_id' => $crewUser->id,
            'tipe_karyawan' => 'bulanan',
            'gaji_pokok' => 2500000,
            'total_bruto' => 2500000,
            'total_bersih' => 2500000,
            'metode_bayar' => 'Transfer Bank',
            'status' => 'paid',
        ]);
    }

    public function test_default_logo_path_resolves_and_file_exists(): void
    {
        $setting = Setting::getSettings();
        $logoPath = $setting->getLogoPath();

        $this->assertNotNull($logoPath, 'Logo path should not be null');
        $this->assertFileExists($logoPath, 'Logo file should exist on disk');
    }

    public function test_all_pdf_routes_use_logo_and_kop(): void
    {
        $setting = Setting::getSettings();
        $logoPath = $setting->getLogoPath();

        // 1. Invoice
        $resInvoice = $this->actingAs($this->admin)->get(route('invoice.download', $this->order));
        $resInvoice->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resInvoice->headers->get('content-type'));

        $htmlInvoice = view('pdf.invoice', ['order' => $this->order, 'setting' => $setting])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlInvoice);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlInvoice);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlInvoice);
        $this->assertStringContainsString('0852-8844-2884', $htmlInvoice);
        $this->assertStringContainsString('@n7cateringbunardi', $htmlInvoice);
        $this->assertStringContainsString($logoPath, $htmlInvoice);

        // 2. Receipt (Kwitansi)
        $resReceipt = $this->actingAs($this->admin)->get(route('receipt.download', $this->payment));
        $resReceipt->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resReceipt->headers->get('content-type'));

        $htmlReceipt = view('pdf.receipt', ['payment' => $this->payment, 'setting' => $setting])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlReceipt);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlReceipt);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlReceipt);
        $this->assertStringContainsString($logoPath, $htmlReceipt);

        // 3. SPK
        $resSpk = $this->actingAs($this->admin)->get(route('spk.download', $this->workOrder));
        $resSpk->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSpk->headers->get('content-type'));

        $htmlSpk = view('pdf.spk', ['workOrder' => $this->workOrder, 'setting' => $setting])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlSpk);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlSpk);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlSpk);
        $this->assertStringContainsString($logoPath, $htmlSpk);

        // 4. Surat Jalan
        $resSj = $this->actingAs($this->admin)->get(route('surat_jalan.download', $this->workOrder));
        $resSj->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSj->headers->get('content-type'));

        $htmlSj = view('pdf.surat-jalan', ['workOrder' => $this->workOrder, 'setting' => $setting])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlSj);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlSj);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlSj);
        $this->assertStringContainsString($logoPath, $htmlSj);

        // 5. Slip Gaji
        $resSlip = $this->actingAs($this->admin)->get(route('payroll.slip', $this->payrollItem));
        $resSlip->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resSlip->headers->get('content-type'));

        $htmlSlip = view('pdf.slip-gaji', [
            'item' => $this->payrollItem,
            'user' => $this->payrollItem->user,
            'payroll' => $this->payrollItem->payroll,
            'setting' => $setting,
        ])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlSlip);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlSlip);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlSlip);
        $this->assertStringContainsString($logoPath, $htmlSlip);

        // 6. Laporan Keuangan
        $resLap = $this->actingAs($this->admin)->get(route('laporan_keuangan.pdf', [
            'year' => 2026,
            'month' => '10',
            'viewMode' => 'kas',
        ]));
        $resLap->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $resLap->headers->get('content-type'));

        // 7. Dynamic Invoice
        $htmlDynamic = view('pdf.invoice_dynamic', [
            'order' => $this->order,
            'setting' => $setting,
            'judul_tagihan' => 'Tagihan Termin 1',
            'nominal_tagihan' => 10000000,
        ])->render();
        $this->assertStringContainsString('IBU NARDI', $htmlDynamic);
        $this->assertStringContainsString('Catering &amp; N7 Decoration', $htmlDynamic);
        $this->assertStringContainsString('Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut', $htmlDynamic);
        $this->assertStringContainsString($logoPath, $htmlDynamic);
    }
}
