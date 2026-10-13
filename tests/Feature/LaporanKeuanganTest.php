<?php

namespace Tests\Feature;

use App\Filament\Pages\LaporanKeuangan;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LaporanKeuanganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_can_access_laporan_keuangan_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/laporan-keuangan')
            ->assertSuccessful()
            ->assertSee('Laporan Keuangan Terpadu & Analisis Bisnis');
    }

    public function test_crew_cannot_access_laporan_keuangan(): void
    {
        $crew = User::factory()->create([
            'role' => 'crew',
        ]);

        $this->actingAs($crew)
            ->get('/laporan-keuangan')
            ->assertForbidden();
    }

    public function test_livewire_laporan_keuangan_computes_kpi_and_charts(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $order = Order::create([
            'no_order'   => 'ORD-TEST-001',
            'nama_acara' => 'Test Wedding Event',
            'tanggal'    => '2026-10-15',
            'status'     => 'proses',
            'total'      => 50000000,
        ]);

        Payment::create([
            'order_id'         => $order->id,
            'amount'           => 20000000,
            'payment_date'     => '2026-10-15',
            'payment_method'   => 'Transfer BCA',
            'reference_number' => 'REF-001',
        ]);

        Expense::create([
            'tanggal'           => '2026-10-15',
            'kategori'          => 'Bahan Baku & Catering',
            'judul'             => 'Belanja Daging',
            'nominal'           => 5000000,
            'metode_pembayaran' => 'Tunai / Cash',
            'pic_id'            => $admin->id,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(LaporanKeuangan::class)
            ->assertSet('year', 2026)
            ->assertSet('month', 'all')
            ->assertSet('viewMode', 'kas');

        $kpi = $component->get('kpiStats');
        $this->assertEquals(50000000, $kpi['totalNilaiOrder']);
        $this->assertEquals(20000000, $kpi['totalKasMasuk']);
        $this->assertEquals(30000000, $kpi['piutangBerjalan']);
        $this->assertEquals(5000000, $kpi['totalBebanOperasional']);
        // Basis kas: 20.000.000 - 5.000.000 = 15.000.000
        $this->assertEquals(15000000, $kpi['labaBersih']);

        // Test switch basis akrual
        $component->call('setViewMode', 'akrual');
        $kpiAkrual = $component->get('kpiStats');
        // Basis akrual: 50.000.000 - 5.000.000 = 45.000.000
        $this->assertEquals(45000000, $kpiAkrual['labaBersih']);

        // Test monthly chart data
        $monthly = $component->get('monthlyChartData');
        $this->assertCount(12, $monthly['labels']);
        $this->assertEquals(20000000, $monthly['kasMasuk'][9]); // Bulan Oktober (index 9)
    }

    public function test_filter_month_narrows_financial_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Order::create([
            'no_order'   => 'ORD-OCT',
            'nama_acara' => 'Event Oktober',
            'tanggal'    => '2026-10-01',
            'total'      => 10000000,
        ]);

        Order::create([
            'no_order'   => 'ORD-NOV',
            'nama_acara' => 'Event November',
            'tanggal'    => '2026-11-01',
            'total'      => 20000000,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(LaporanKeuangan::class)
            ->set('year', 2026)
            ->set('month', '10');

        $kpi = $component->get('kpiStats');
        $this->assertEquals(10000000, $kpi['totalNilaiOrder']);
        $this->assertEquals(1, $kpi['totalOrderCount']);
    }

    public function test_admin_can_download_laporan_keuangan_pdf_yearly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create an order and expense so tables have records
        $order = Order::create([
            'no_order'   => 'ORD-2026-Y01',
            'nama_acara' => 'Pernikahan Annual Test',
            'tanggal'    => '2026-05-20',
            'total'      => 45000000,
        ]);
        Payment::create([
            'order_id'       => $order->id,
            'amount'         => 30000000,
            'payment_date'   => '2026-05-20',
            'payment_method' => 'Transfer BCA',
        ]);
        Expense::create([
            'tanggal'           => '2026-05-21',
            'kategori'          => 'Bahan Baku & Catering',
            'judul'             => 'Belanja Beras & Daging',
            'nominal'           => 12000000,
            'metode_pembayaran' => 'Transfer',
            'pic_id'            => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('laporan_keuangan.pdf', [
                'year'     => 2026,
                'month'    => 'all',
                'viewMode' => 'kas',
            ]));

        $response->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        $pdfContent = $response->getContent();
        // Memastikan dokumen PDF berhasil terkompilasi dan memuat minimal 3 halaman
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdfContent);
        $this->assertGreaterThanOrEqual(3, $pageCount, "Laporan tahunan harus terbagi dalam beberapa halaman (minimal 3 halaman)");
    }

    public function test_admin_can_download_laporan_keuangan_pdf_monthly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $order = Order::create([
            'no_order'   => 'ORD-2026-M10',
            'nama_acara' => 'Pernikahan Bulan Oktober',
            'tanggal'    => '2026-10-18',
            'total'      => 60000000,
        ]);
        Payment::create([
            'order_id'       => $order->id,
            'amount'         => 40000000,
            'payment_date'   => '2026-10-18',
            'payment_method' => 'Transfer Mandiri',
        ]);
        Expense::create([
            'tanggal'           => '2026-10-19',
            'kategori'          => 'Dekorasi & Pelaminan',
            'judul'             => 'Bunga Segar & Properti Pelaminan',
            'nominal'           => 15000000,
            'metode_pembayaran' => 'Cash',
            'pic_id'            => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('laporan_keuangan.pdf', [
                'year'     => 2026,
                'month'    => '10',
                'viewMode' => 'akrual',
            ]));

        $response->assertSuccessful();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        $pdfContent = $response->getContent();
        // Memastikan dokumen PDF bulanan juga terbagi dalam beberapa halaman (minimal 3 halaman)
        $pageCount = preg_match_all('/\/Type\s*\/Page\b/', $pdfContent);
        $this->assertGreaterThanOrEqual(3, $pageCount, "Laporan bulanan harus terbagi dalam beberapa halaman (minimal 3 halaman)");
    }
}

