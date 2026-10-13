<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Expense;
use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'payments']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'setting' => $setting
        ])->setPaper('a4', 'portrait');
        
        return $pdf->stream('invoice-'.$order->no_order.'.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['order.customer', 'invoice']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'setting' => $setting
        ]);
        
        return $pdf->stream('kwitansi-'.$payment->reference_number.'.pdf');
    }

    public function spk(\App\Models\WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.spk', [
            'workOrder' => $workOrder,
            'setting' => $setting
        ]);
        
        return $pdf->stream('SPK-'.$workOrder->spk_number.'.pdf');
    }

    public function suratJalan(\App\Models\WorkOrder $workOrder)
    {
        $workOrder->load(['order.customer', 'pic', 'users.user', 'equipment.inventory']);
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('pdf.surat-jalan', [
            'workOrder' => $workOrder,
            'setting' => $setting
        ]);
        
        return $pdf->stream('SuratJalan-'.$workOrder->surat_jalan_number.'.pdf');
    }

    public function slipGaji(\App\Models\PayrollItem $payrollItem)
    {
        $payrollItem->load(['user.employeeProfile', 'payroll']);
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'item'    => $payrollItem,
            'user'    => $payrollItem->user,
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

        $setting = Setting::first();

        // 1. Order (Omzet Nilai Kontrak)
        $orderQuery = Order::query()->whereYear('tanggal', $year);
        if ($month !== 'all') {
            $orderQuery->whereMonth('tanggal', (int) $month);
        }
        $totalNilaiOrder = (float) $orderQuery->sum('total');
        $totalOrderCount = $orderQuery->count();

        // 2. Pembayaran Kas Masuk (Real Cash-in)
        $paymentQuery = Payment::query()->whereYear('payment_date', $year);
        if ($month !== 'all') {
            $paymentQuery->whereMonth('payment_date', (int) $month);
        }
        $totalKasMasuk = (float) $paymentQuery->sum('amount');

        // 3. Piutang Pelanggan
        $piutangBerjalan = max(0, $totalNilaiOrder - $totalKasMasuk);

        // 4. Beban Pengeluaran Operasional
        $expenseQuery = Expense::query()->whereYear('tanggal', $year);
        if ($month !== 'all') {
            $expenseQuery->whereMonth('tanggal', (int) $month);
        }
        $totalBebanOperasional = (float) $expenseQuery->sum('nominal');

        $expenseBreakdown = $expenseQuery->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->get();

        // 5. Beban Penggajian (Payroll)
        $payrollQuery = \App\Models\PayrollItem::whereHas('payroll', function ($q) use ($year, $month) {
            $q->whereYear('created_at', $year);
            if ($month !== 'all') {
                $q->whereMonth('created_at', (int) $month);
            }
        });
        $totalBebanPayroll = (float) $payrollQuery->sum('total_bersih');

        // Total Beban & Laba
        $totalBeban = $totalBebanOperasional + $totalBebanPayroll;
        $pendapatanAcuan = $viewMode === 'kas' ? $totalKasMasuk : $totalNilaiOrder;
        $labaBersih = $pendapatanAcuan - $totalBeban;
        $profitMargin = $pendapatanAcuan > 0 ? round(($labaBersih / $pendapatanAcuan) * 100, 1) : 0;
        $collectionRate = $totalNilaiOrder > 0 ? round(($totalKasMasuk / $totalNilaiOrder) * 100, 1) : 0;
        $expenseRatio = $pendapatanAcuan > 0 ? round(($totalBeban / $pendapatanAcuan) * 100, 1) : 0;

        // Label Periode
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = $month === 'all'
            ? "Tahun {$year} (Januari - Desember)"
            : ($namaBulan[(int) $month] ?? "Bulan {$month}") . " {$year}";

        // Ringkasan Bulanan (Jika periode tahunan)
        $monthlySummary = [];
        if ($month === 'all') {
            for ($m = 1; $m <= 12; $m++) {
                $mIn = (float) Payment::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount');
                $mOrd = (float) Order::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->sum('total');
                $mExp = (float) Expense::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->sum('nominal');
                $mPay = (float) \App\Models\PayrollItem::whereHas('payroll', function ($q) use ($year, $m) {
                    $q->whereYear('created_at', $year)->whereMonth('created_at', $m);
                })->sum('total_bersih');
                $mTotBeban = $mExp + $mPay;
                $mAcc = $viewMode === 'kas' ? $mIn : $mOrd;

                $monthlySummary[] = [
                    'bulan'      => $namaBulan[$m],
                    'pendapatan' => $mAcc,
                    'beban_ops'  => $mExp,
                    'beban_gaji' => $mPay,
                    'total_beban'=> $mTotBeban,
                    'laba_bersih'=> $mAcc - $mTotBeban,
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-keuangan', [
            'setting'               => $setting,
            'year'                  => $year,
            'month'                 => $month,
            'viewMode'              => $viewMode,
            'periodeLabel'          => $periodeLabel,
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
            'expenseBreakdown'      => $expenseBreakdown,
            'monthlySummary'        => $monthlySummary,
        ])->setPaper('a4', 'portrait');

        $cleanPeriode = str_replace([' ', '/', '\\'], '-', $periodeLabel);
        return $pdf->stream("Laporan-Keuangan-{$cleanPeriode}.pdf");
    }
}
