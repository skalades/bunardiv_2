<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PayrollItem;
use App\Models\Setting;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        $setting = Setting::getSettings();

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'setting' => $setting,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-'.$order->no_order.'.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['order.customer', 'invoice']);
        $setting = Setting::getSettings();

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'setting' => $setting,
        ]);

        return $pdf->stream('kwitansi-'.$payment->reference_number.'.pdf');
    }

    public function spk(WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::getSettings();

        $pdf = Pdf::loadView('pdf.spk', [
            'workOrder' => $workOrder,
            'setting' => $setting,
        ]);

        return $pdf->stream('SPK-'.$workOrder->spk_number.'.pdf');
    }

    public function suratJalan(WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::getSettings();

        $pdf = Pdf::loadView('pdf.surat-jalan', [
            'workOrder' => $workOrder,
            'setting' => $setting,
        ]);

        return $pdf->stream('SuratJalan-'.$workOrder->surat_jalan_number.'.pdf');
    }

    public function slipGaji(PayrollItem $payrollItem)
    {
        $payrollItem->load(['user.employeeProfile', 'payroll']);
        $setting = Setting::getSettings();

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'item' => $payrollItem,
            'user' => $payrollItem->user,
            'payroll' => $payrollItem->payroll,
            'setting' => $setting,
        ])->setPaper('a5', 'landscape');

        return $pdf->stream('Slip-Gaji-'.$payrollItem->user->name.'-'.$payrollItem->payroll->nama_bulan.'.pdf');
    }

    public function laporanKeuangan(Request $request)
    {
        $year = (int) ($request->query('year') ?? now()->year);
        $month = $request->query('month') ?? 'all';
        $viewMode = $request->query('viewMode') ?? 'kas';

        $setting = Setting::getSettings();

        // 1. Order (Omzet Nilai Kontrak) & Daftar Pesanan Periode Ini
        $ordersQuery = Order::query()
            ->with(['customer', 'payments'])
            ->whereYear('tanggal', $year);
        if ($month !== 'all') {
            $ordersQuery->whereMonth('tanggal', (int) $month);
        }
        $orders = $ordersQuery->orderBy('tanggal', 'asc')->get();
        $totalNilaiOrder = (float) $orders->sum('total');
        $totalOrderCount = $orders->count();

        // 2. Pembayaran Kas Masuk (Real Cash-in)
        $paymentQuery = Payment::query()->whereYear('payment_date', $year);
        if ($month !== 'all') {
            $paymentQuery->whereMonth('payment_date', (int) $month);
        }
        $totalKasMasuk = (float) $paymentQuery->sum('amount');

        // 3. Piutang Pelanggan
        $piutangBerjalan = max(0, $totalNilaiOrder - $totalKasMasuk);

        // 4. Beban Pengeluaran Operasional (Detail & Agregasi Kategori)
        $expenseQuery = Expense::query()->whereYear('tanggal', $year);
        if ($month !== 'all') {
            $expenseQuery->whereMonth('tanggal', (int) $month);
        }
        $allExpenses = (clone $expenseQuery)->orderBy('tanggal', 'asc')->get();
        $totalBebanOperasional = (float) $allExpenses->sum('nominal');

        $expenseBreakdown = (clone $expenseQuery)->selectRaw('kategori, count(*) as count, sum(nominal) as total')
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        // 5. Beban Penggajian (Payroll)
        $payrollQuery = PayrollItem::with(['user', 'payroll'])
            ->whereHas('payroll', function ($q) use ($year, $month) {
                $q->whereYear('created_at', $year);
                if ($month !== 'all') {
                    $q->whereMonth('created_at', (int) $month);
                }
            });
        $payrollItems = $payrollQuery->get();
        $totalBebanPayroll = (float) $payrollItems->sum('total_bersih');

        // Total Beban, Laba, dan Indikator Kinerja Keuangan
        $totalBeban = $totalBebanOperasional + $totalBebanPayroll;
        $pendapatanAcuan = $viewMode === 'kas' ? $totalKasMasuk : $totalNilaiOrder;
        $labaBersih = $pendapatanAcuan - $totalBeban;
        $profitMargin = $pendapatanAcuan > 0 ? round(($labaBersih / $pendapatanAcuan) * 100, 1) : 0;
        $collectionRate = $totalNilaiOrder > 0 ? round(($totalKasMasuk / $totalNilaiOrder) * 100, 1) : 0;
        $expenseRatio = $pendapatanAcuan > 0 ? round(($totalBeban / $pendapatanAcuan) * 100, 1) : 0;
        $netCashFlow = $totalKasMasuk - $totalBeban;

        // Label Periode & Referensi Dokumen
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $periodeLabel = $month === 'all'
            ? "Tahun {$year} (Januari - Desember)"
            : ($namaBulan[(int) $month] ?? "Bulan {$month}")." {$year}";

        $docRef = sprintf('FIN-%04d-%s', $year, $month !== 'all' ? sprintf('%02d', (int) $month) : 'ANNUAL');

        // Path logo perusahaan jika tersedia
        $companyLogoPath = $setting->getLogoPath();

        // Matriks Rekapitulasi 12 Bulan & Kuartal (Jika periode tahunan)
        $monthlySummary = [];
        $quarterlySummary = [
            'Q1' => ['label' => 'Kuartal I (Jan - Mar)', 'pendapatan' => 0, 'beban_ops' => 0, 'beban_gaji' => 0, 'total_beban' => 0, 'laba_bersih' => 0],
            'Q2' => ['label' => 'Kuartal II (Apr - Jun)', 'pendapatan' => 0, 'beban_ops' => 0, 'beban_gaji' => 0, 'total_beban' => 0, 'laba_bersih' => 0],
            'Q3' => ['label' => 'Kuartal III (Jul - Sep)', 'pendapatan' => 0, 'beban_ops' => 0, 'beban_gaji' => 0, 'total_beban' => 0, 'laba_bersih' => 0],
            'Q4' => ['label' => 'Kuartal IV (Okt - Des)', 'pendapatan' => 0, 'beban_ops' => 0, 'beban_gaji' => 0, 'total_beban' => 0, 'laba_bersih' => 0],
        ];

        if ($month === 'all') {
            $paymentsByMonth = Payment::whereYear('payment_date', $year)
                ->get(['payment_date', 'amount'])
                ->groupBy(fn ($p) => (int) Carbon::parse($p->payment_date)->month)
                ->map(fn ($g) => (float) $g->sum('amount'));

            $ordersByMonth = Order::whereYear('tanggal', $year)
                ->get(['tanggal', 'total'])
                ->groupBy(fn ($o) => (int) Carbon::parse($o->tanggal)->month)
                ->map(fn ($g) => (float) $g->sum('total'));

            $expensesByMonth = Expense::whereYear('tanggal', $year)
                ->get(['tanggal', 'nominal'])
                ->groupBy(fn ($e) => (int) Carbon::parse($e->tanggal)->month)
                ->map(fn ($g) => (float) $g->sum('nominal'));

            $payrollsByMonth = PayrollItem::whereHas('payroll', fn ($q) => $q->whereYear('created_at', $year))
                ->with('payroll:id,created_at')
                ->get(['payroll_id', 'total_bersih'])
                ->groupBy(fn ($p) => (int) Carbon::parse($p->payroll?->created_at)->month)
                ->map(fn ($g) => (float) $g->sum('total_bersih'));

            for ($m = 1; $m <= 12; $m++) {
                $mIn = (float) ($paymentsByMonth[$m] ?? 0);
                $mOrd = (float) ($ordersByMonth[$m] ?? 0);
                $mExp = (float) ($expensesByMonth[$m] ?? 0);
                $mPay = (float) ($payrollsByMonth[$m] ?? 0);
                $mTotBeban = $mExp + $mPay;
                $mAcc = $viewMode === 'kas' ? $mIn : $mOrd;
                $mNet = $mAcc - $mTotBeban;
                $mMargin = $mAcc > 0 ? round(($mNet / $mAcc) * 100, 1) : 0;

                $monthlySummary[] = [
                    'bulan_num' => $m,
                    'bulan' => $namaBulan[$m],
                    'pendapatan' => $mAcc,
                    'beban_ops' => $mExp,
                    'beban_gaji' => $mPay,
                    'total_beban' => $mTotBeban,
                    'laba_bersih' => $mNet,
                    'margin' => $mMargin,
                ];

                $qKey = $m <= 3 ? 'Q1' : ($m <= 6 ? 'Q2' : ($m <= 9 ? 'Q3' : 'Q4'));
                $quarterlySummary[$qKey]['pendapatan'] += $mAcc;
                $quarterlySummary[$qKey]['beban_ops'] += $mExp;
                $quarterlySummary[$qKey]['beban_gaji'] += $mPay;
                $quarterlySummary[$qKey]['total_beban'] += $mTotBeban;
                $quarterlySummary[$qKey]['laba_bersih'] += $mNet;
            }
        }

        // Pengeluaran teratas untuk kompatibilitas
        $topExpenses = $allExpenses->take(15);

        $pdf = Pdf::loadView('pdf.laporan-keuangan', [
            'setting' => $setting,
            'companyLogoPath' => $companyLogoPath,
            'year' => $year,
            'month' => $month,
            'viewMode' => $viewMode,
            'periodeLabel' => $periodeLabel,
            'docRef' => $docRef,
            'totalNilaiOrder' => $totalNilaiOrder,
            'totalOrderCount' => $totalOrderCount,
            'totalKasMasuk' => $totalKasMasuk,
            'piutangBerjalan' => $piutangBerjalan,
            'totalBebanOperasional' => $totalBebanOperasional,
            'totalBebanPayroll' => $totalBebanPayroll,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,
            'profitMargin' => $profitMargin,
            'collectionRate' => $collectionRate,
            'expenseRatio' => $expenseRatio,
            'netCashFlow' => $netCashFlow,
            'expenseBreakdown' => $expenseBreakdown,
            'monthlySummary' => $monthlySummary,
            'quarterlySummary' => $quarterlySummary,
            'topExpenses' => $topExpenses,
            'allExpenses' => $allExpenses,
            'orders' => $orders,
            'payrollItems' => $payrollItems,
        ])->setPaper('a4', 'portrait');

        $cleanPeriode = str_replace([' ', '/', '\\'], '-', $periodeLabel);

        return $pdf->stream("Laporan-Keuangan-{$cleanPeriode}.pdf");
    }
}
