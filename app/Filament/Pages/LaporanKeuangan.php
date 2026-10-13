<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Carbon\Carbon;
use Filament\Pages\Page;

class LaporanKeuangan extends Page
{
    protected static ?string $slug = 'laporan-keuangan';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-presentation-chart-line';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'LAPORAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Keuangan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string
    {
        return 'Laporan Keuangan Terpadu & Analisis Bisnis';
    }

    protected string $view = 'filament.pages.laporan-keuangan';

    // State Filter
    public int $year;
    public string $month = 'all'; // 'all' or '1'..'12'
    public string $viewMode = 'kas'; // 'kas' (Arus Kas Diterima) atau 'akrual' (Nilai Kontrak Pesanan)

    public function mount(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = 'all';
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['kas', 'akrual'])) {
            $this->viewMode = $mode;
        }
    }

    public function getAvailableYearsProperty(): array
    {
        $currentYear = Carbon::now()->year;
        $years = [];
        for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++) {
            $years[] = $y;
        }
        return $years;
    }

    public function getAvailableMonthsProperty(): array
    {
        return [
            'all' => 'Semua Bulan (Setahun Penuh)',
            '1'   => 'Januari',
            '2'   => 'Februari',
            '3'   => 'Maret',
            '4'   => 'April',
            '5'   => 'Mei',
            '6'   => 'Juni',
            '7'   => 'Juli',
            '8'   => 'Agustus',
            '9'   => 'September',
            '10'  => 'Oktober',
            '11'  => 'November',
            '12'  => 'Desember',
        ];
    }

    public function getPeriodeLabelProperty(): string
    {
        if ($this->month === 'all') {
            return 'Tahun ' . $this->year;
        }

        $months = $this->getAvailableMonthsProperty();
        return ($months[$this->month] ?? '') . ' ' . $this->year;
    }

    /**
     * Hitung Ringkasan KPI Utama berdasarkan filter aktif
     */
    public function getKpiStatsProperty(): array
    {
        // 1. Order (Omzet Nilai Kontrak)
        $orderQuery = Order::query()->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $orderQuery->whereMonth('tanggal', (int) $this->month);
        }
        $totalNilaiOrder = (float) $orderQuery->sum('total');
        $totalOrderCount = $orderQuery->count();

        // 2. Pembayaran Kas Masuk (Real Cash-in)
        $paymentQuery = Payment::query()->whereYear('payment_date', $this->year);
        if ($this->month !== 'all') {
            $paymentQuery->whereMonth('payment_date', (int) $this->month);
        }
        $totalKasMasuk = (float) $paymentQuery->sum('amount');

        // 3. Piutang Pelanggan Periode Ini
        $piutangBerjalan = max(0, $totalNilaiOrder - $totalKasMasuk);

        // 4. Beban Pengeluaran Operasional (Expenses)
        $expenseQuery = Expense::query()->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $expenseQuery->whereMonth('tanggal', (int) $this->month);
        }
        $totalBebanOperasional = (float) $expenseQuery->sum('nominal');

        // 5. Beban Penggajian (Payroll)
        $payrollQuery = PayrollItem::whereHas('payroll', function ($q) {
            $q->whereYear('created_at', $this->year);
            if ($this->month !== 'all') {
                $q->whereMonth('created_at', (int) $this->month);
            }
        });
        $totalBebanPayroll = (float) $payrollQuery->sum('total_bersih');

        // Total Beban
        $totalBeban = $totalBebanOperasional + $totalBebanPayroll;

        // Laba Bersih
        // Basis Kas: Kas Masuk - Total Beban
        // Basis Akrual: Nilai Order - Total Beban
        $pendapatanAcuan = $this->viewMode === 'kas' ? $totalKasMasuk : $totalNilaiOrder;
        $labaBersih = $pendapatanAcuan - $totalBeban;
        $profitMargin = $pendapatanAcuan > 0 ? round(($labaBersih / $pendapatanAcuan) * 100, 1) : 0;
        $collectionRate = $totalNilaiOrder > 0 ? round(($totalKasMasuk / $totalNilaiOrder) * 100, 1) : 0;
        $expenseRatio = $pendapatanAcuan > 0 ? round(($totalBeban / $pendapatanAcuan) * 100, 1) : 0;
        $netCashFlow = $totalKasMasuk - $totalBeban;

        return [
            'totalNilaiOrder'       => $totalNilaiOrder,
            'totalOrderCount'       => $totalOrderCount,
            'totalKasMasuk'         => $totalKasMasuk,
            'piutangBerjalan'       => $piutangBerjalan,
            'totalBebanOperasional' => $totalBebanOperasional,
            'totalBebanPayroll'     => $totalBebanPayroll,
            'totalBeban'            => $totalBeban,
            'labaBersih'            => $labaBersih,
            'profitMargin'          => $profitMargin,
            'collectionRate'        => $collectionRate,
            'expenseRatio'          => $expenseRatio,
            'netCashFlow'           => $netCashFlow,
        ];
    }

    /**
     * Data Tren 12 Bulan (Jan - Des) untuk Chart Laba Rugi & Arus Kas
     */
    public function getMonthlyChartDataProperty(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $kasMasuk = [];
        $nilaiOrder = [];
        $bebanOps = [];
        $bebanPayroll = [];
        $totalBeban = [];
        $labaBersihKas = [];
        $labaBersihAkrual = [];

        for ($m = 1; $m <= 12; $m++) {
            $inflow = (float) Payment::whereYear('payment_date', $this->year)->whereMonth('payment_date', $m)->sum('amount');
            $orderVal = (float) Order::whereYear('tanggal', $this->year)->whereMonth('tanggal', $m)->sum('total');
            $ops = (float) Expense::whereYear('tanggal', $this->year)->whereMonth('tanggal', $m)->sum('nominal');
            $pay = (float) PayrollItem::whereHas('payroll', function ($q) use ($m) {
                $q->whereYear('created_at', $this->year)->whereMonth('created_at', $m);
            })->sum('total_bersih');

            $totBeban = $ops + $pay;

            $kasMasuk[] = $inflow;
            $nilaiOrder[] = $orderVal;
            $bebanOps[] = $ops;
            $bebanPayroll[] = $pay;
            $totalBeban[] = $totBeban;
            $labaBersihKas[] = $inflow - $totBeban;
            $labaBersihAkrual[] = $orderVal - $totBeban;
        }

        return [
            'labels'           => $months,
            'kasMasuk'         => $kasMasuk,
            'nilaiOrder'       => $nilaiOrder,
            'bebanOps'         => $bebanOps,
            'bebanPayroll'     => $bebanPayroll,
            'totalBeban'       => $totalBeban,
            'labaBersihKas'    => $labaBersihKas,
            'labaBersihAkrual' => $labaBersihAkrual,
        ];
    }

    /**
     * Data Breakdown Pengeluaran per Kategori untuk Donut Chart
     */
    public function getExpenseCategoryDataProperty(): array
    {
        $query = Expense::query()->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $query->whereMonth('tanggal', (int) $this->month);
        }

        $categories = $query->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $values = [];
        $colors = [
            '#cfa24b', // Gold
            '#e67e22', // Orange
            '#3498db', // Blue
            '#9b59b6', // Purple
            '#2ecc71', // Green
            '#e74c3c', // Red
            '#1abc9c', // Teal
            '#95a5a6', // Gray
        ];

        foreach ($categories as $cat) {
            $labels[] = $cat->kategori;
            $values[] = (float) $cat->total;
        }

        // Jika ada payroll pada periode ini, tambahkan beban payroll sebagai komponen visual
        $kpi = $this->kpiStats;
        if ($kpi['totalBebanPayroll'] > 0) {
            $labels[] = 'Beban Gaji & Upah Kru';
            $values[] = $kpi['totalBebanPayroll'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }

    /**
     * Rincian Laporan Laba Rugi (P&L Income Statement) Tabel Akuntansi
     */
    public function getIncomeStatementRowsProperty(): array
    {
        $kpi = $this->kpiStats;

        // Breakdown Pengeluaran
        $expenseQuery = Expense::query()->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $expenseQuery->whereMonth('tanggal', (int) $this->month);
        }
        $expenseBreakdown = $expenseQuery->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        return [
            'pendapatan' => [
                'omzet_order'   => $kpi['totalNilaiOrder'],
                'kas_diterima'  => $kpi['totalKasMasuk'],
                'piutang'       => $kpi['piutangBerjalan'],
            ],
            'beban_operasional' => $expenseBreakdown,
            'beban_sdm' => [
                'total_payroll' => $kpi['totalBebanPayroll'],
            ],
            'ringkasan' => [
                'total_pendapatan' => $this->viewMode === 'kas' ? $kpi['totalKasMasuk'] : $kpi['totalNilaiOrder'],
                'total_beban'      => $kpi['totalBeban'],
                'laba_bersih'      => $kpi['labaBersih'],
                'profit_margin'    => $kpi['profitMargin'],
            ],
        ];
    }

    /**
     * Daftar Pesanan / Kontrak Acara pada periode terpilih
     */
    public function getOrdersProperty()
    {
        $query = Order::query()
            ->with(['customer', 'payments'])
            ->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $query->whereMonth('tanggal', (int) $this->month);
        }
        return $query->orderBy('tanggal', 'asc')->get();
    }

    /**
     * Buku Catatan Transaksi Pengeluaran Operasional pada periode terpilih
     */
    public function getExpensesListProperty()
    {
        $query = Expense::query()
            ->whereYear('tanggal', $this->year);
        if ($this->month !== 'all') {
            $query->whereMonth('tanggal', (int) $this->month);
        }
        return $query->orderBy('tanggal', 'asc')->get();
    }

    /**
     * Matriks Rekapitulasi Finansial 12 Bulan (Tahun Berjalan)
     */
    public function getMonthlySummaryProperty(): array
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $summary = [];
        for ($m = 1; $m <= 12; $m++) {
            $inflow = (float) Payment::whereYear('payment_date', $this->year)->whereMonth('payment_date', $m)->sum('amount');
            $orderVal = (float) Order::whereYear('tanggal', $this->year)->whereMonth('tanggal', $m)->sum('total');
            $ops = (float) Expense::whereYear('tanggal', $this->year)->whereMonth('tanggal', $m)->sum('nominal');
            $pay = (float) PayrollItem::whereHas('payroll', function ($q) use ($m) {
                $q->whereYear('created_at', $this->year)->whereMonth('created_at', $m);
            })->sum('total_bersih');
            $totBeban = $ops + $pay;
            $acc = $this->viewMode === 'kas' ? $inflow : $orderVal;
            $net = $acc - $totBeban;
            $margin = $acc > 0 ? round(($net / $acc) * 100, 1) : 0;

            $summary[] = [
                'bulan_num'   => $m,
                'bulan'       => $namaBulan[$m],
                'pendapatan'  => $acc,
                'beban_ops'   => $ops,
                'beban_gaji'  => $pay,
                'total_beban' => $totBeban,
                'laba_bersih' => $net,
                'margin'      => $margin,
            ];
        }

        return $summary;
    }
}
