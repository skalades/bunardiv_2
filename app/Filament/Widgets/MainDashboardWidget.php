<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PayrollItem;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class MainDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.main-dashboard-widget';
    protected int | string | array $columnSpan = 'full';

    public string $scheduleFilter = '7_days'; // '7_days' or 'today'

    public function setScheduleFilter(string $filter): void
    {
        if (in_array($filter, ['7_days', 'today'])) {
            $this->scheduleFilter = $filter;
        }
    }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function getDashboardStatsProperty(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // 1. Pesanan Hari Ini & Menunggu Diproses
        $todayOrdersCount = Order::whereDate('tanggal', $today)->count();
        $pendingOrdersCount = Order::whereIn('status', ['pesanan masuk', 'proses', 'draft'])->count();

        // 2. Total Pemasukan Riil dari Pembayaran Masuk
        $totalPemasukan = (float) Payment::sum('amount');
        $totalPembayaranCount = Payment::count();

        // 3. Pengeluaran Riil (Operasional + Payroll SDM)
        $bebanOps = (float) Expense::sum('nominal');
        $bebanGaji = (float) PayrollItem::sum('total_bersih');
        $totalPengeluaran = $bebanOps + $bebanGaji;

        // 4. Piutang Pelanggan & Tagihan Belum Lunas
        $orders = Order::with('payments')->where('status', '!=', 'dibatalkan')->get();
        $totalKontrak = 0;
        $totalTerbayar = 0;
        $unpaidInvoicesCount = 0;

        foreach ($orders as $order) {
            $orderTotal = (float) $order->total;
            $orderPaid = (float) $order->payments->sum('amount');
            $totalKontrak += $orderTotal;
            $totalTerbayar += $orderPaid;
            if ($orderTotal > $orderPaid) {
                $unpaidInvoicesCount++;
            }
        }
        $piutangPelanggan = max(0, $totalKontrak - $totalTerbayar);

        // 5. Estimasi Profit Bulan Ini
        $pemasukanBulanIni = (float) Payment::whereYear('payment_date', $thisYear)
            ->whereMonth('payment_date', $thisMonth)
            ->sum('amount');

        $opsBulanIni = (float) Expense::whereYear('tanggal', $thisYear)
            ->whereMonth('tanggal', $thisMonth)
            ->sum('nominal');

        $gajiBulanIni = (float) PayrollItem::whereHas('payroll', function ($q) use ($thisYear, $thisMonth) {
            $q->whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth);
        })->sum('total_bersih');

        $pengeluaranBulanIni = $opsBulanIni + $gajiBulanIni;
        $profitBulanIni = $pemasukanBulanIni - $pengeluaranBulanIni;

        $pesananSelesaiBulanIni = Order::whereYear('tanggal', $thisYear)
            ->whereMonth('tanggal', $thisMonth)
            ->where('status', 'selesai')
            ->count();

        // 6. Notifikasi & Presensi Kru Hari Ini
        $crewIds = User::where('role', 'crew')->pluck('id');
        $totalCrew = $crewIds->count();
        $crewHadir = Attendance::whereIn('user_id', $crewIds)
            ->whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->distinct('user_id')
            ->count('user_id');
        $crewBelum = max(0, $totalCrew - $crewHadir);

        return [
            'todayOrdersCount'       => $todayOrdersCount,
            'pendingOrdersCount'     => $pendingOrdersCount,
            'totalPemasukan'         => $totalPemasukan,
            'totalPembayaranCount'   => $totalPembayaranCount,
            'totalPengeluaran'       => $totalPengeluaran,
            'bebanOps'               => $bebanOps,
            'bebanGaji'              => $bebanGaji,
            'piutangPelanggan'       => $piutangPelanggan,
            'unpaidInvoicesCount'    => $unpaidInvoicesCount,
            'profitBulanIni'         => $profitBulanIni,
            'pemasukanBulanIni'      => $pemasukanBulanIni,
            'pengeluaranBulanIni'    => $pengeluaranBulanIni,
            'pesananSelesaiBulanIni' => $pesananSelesaiBulanIni,
            'totalCrew'              => $totalCrew,
            'crewHadir'              => $crewHadir,
            'crewBelum'              => $crewBelum,
            'upcomingOrdersCount'    => Order::whereBetween('tanggal', [Carbon::today()->toDateString(), Carbon::today()->addDays(7)->toDateString()])
                ->where('status', '!=', 'dibatalkan')
                ->count(),
        ];
    }

    public function getTodayOrdersProperty()
    {
        return Order::with('customer')
            ->whereDate('tanggal', Carbon::today())
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('jam', 'asc')
            ->get();
    }

    public function getUpcomingOrdersProperty()
    {
        $today = Carbon::today();

        if ($this->scheduleFilter === 'today') {
            return $this->today_orders;
        }

        return Order::with('customer')
            ->whereBetween('tanggal', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();
    }

    public function getRecentOrdersProperty()
    {
        return Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}
