<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class KalenderAcara extends Page
{
    protected static ?string $slug = 'kalender';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'MANAJEMEN ORDER';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kalender Jadwal';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string
    {
        return 'Kalender Jadwal Acara';
    }

    protected string $view = 'filament.pages.kalender-acara';

    // State properties
    public int $year;
    public int $month;
    public string $viewMode = 'month'; // 'month', 'week', 'agenda'
    public string $filterStatus = 'all';
    public string $filterJenis = 'all';
    public string $search = '';
    public bool $showPrepDates = true;

    // Selected items for modal and day-panel
    public ?int $selectedOrderId = null;
    public ?string $selectedDate = null;
    public int $weekOffset = 0;

    public function mount(): void
    {
        $now = Carbon::now();
        $this->year = $now->year;
        $this->month = $now->month;
        $this->selectedDate = $now->toDateString();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function today(): void
    {
        $now = Carbon::now();
        $this->year = $now->year;
        $this->month = $now->month;
        $this->selectedDate = $now->toDateString();
        $this->weekOffset = 0;
    }

    public function setMonth(int $month): void
    {
        $this->month = max(1, min(12, $month));
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function previousWeek(): void
    {
        $this->weekOffset--;
    }

    public function nextWeek(): void
    {
        $this->weekOffset++;
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['month', 'week', 'agenda'])) {
            $this->viewMode = $mode;
        }
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
    }

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
    }

    public function closeModal(): void
    {
        $this->selectedOrderId = null;
    }

    public function getIndonesianMonthNameProperty(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return ($months[$this->month] ?? '') . ' ' . $this->year;
    }

    public function getAvailableMonthsProperty(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    public function getAvailableYearsProperty(): array
    {
        $currentYear = Carbon::now()->year;
        $years = [];
        for ($y = $currentYear - 2; $y <= $currentYear + 3; $y++) {
            $years[] = $y;
        }
        return $years;
    }

    public function getJenisAcaraListProperty(): array
    {
        return Order::whereNotNull('jenis_acara')
            ->where('jenis_acara', '!=', '')
            ->distinct()
            ->pluck('jenis_acara')
            ->toArray();
    }

    /**
     * Get Base Query for Orders
     */
    protected function getFilteredOrdersQuery()
    {
        $query = Order::with(['customer', 'workOrders.pic', 'workOrders.users.user']);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterJenis !== 'all') {
            $query->where('jenis_acara', $this->filterJenis);
        }

        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_acara', 'like', $term)
                  ->orWhere('no_order', 'like', $term)
                  ->orWhere('venue', 'like', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('nama', 'like', $term)
                         ->orWhere('no_telp', 'like', $term);
                  });
            });
        }

        return $query;
    }

    /**
     * Monthly Stats KPI Summary
     */
    public function getMonthStatsProperty(): array
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth()->toDateString();

        $monthlyOrders = Order::whereBetween('tanggal', [$startDate, $endDate])->get();

        $totalCount = $monthlyOrders->count();
        $totalNominal = $monthlyOrders->sum('total');
        $masukCount = $monthlyOrders->filter(fn ($o) => in_array(strtolower($o->status), ['pesanan masuk', 'baru']))->count();
        $prosesCount = $monthlyOrders->filter(fn ($o) => in_array(strtolower($o->status), ['proses', 'persiapan', 'dikonfirmasi', 'berlangsung']))->count();
        $selesaiCount = $monthlyOrders->filter(fn ($o) => strtolower($o->status) === 'selesai')->count();
        $batalCount = $monthlyOrders->filter(fn ($o) => strtolower($o->status) === 'dibatalkan')->count();

        // Detect high-workload days (dates with >= 2 events)
        $peakDates = $monthlyOrders->groupBy('tanggal')
            ->filter(fn ($group) => $group->count() >= 2)
            ->keys()
            ->toArray();

        return [
            'total' => $totalCount,
            'nominal' => $totalNominal,
            'masuk' => $masukCount,
            'proses' => $prosesCount,
            'selesai' => $selesaiCount,
            'batal' => $batalCount,
            'peakDates' => $peakDates,
        ];
    }

    /**
     * Data array of all calendar days in current month grid
     */
    public function getCalendarWeeksProperty(): array
    {
        $firstOfMonth = Carbon::createFromDate($this->year, $this->month, 1);
        $lastOfMonth = $firstOfMonth->copy()->endOfMonth();

        // Query orders in range (from start of first week to end of last week)
        $startOfGrid = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $lastOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $orders = $this->getFilteredOrdersQuery()
            ->whereBetween('tanggal', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
            ->orderBy('jam', 'asc')
            ->get()
            ->groupBy('tanggal');

        // Query work order prep dates if enabled
        $prepWorkOrders = collect();
        if ($this->showPrepDates) {
            $prepWorkOrders = WorkOrder::with(['order.customer', 'pic'])
                ->whereBetween('tanggal_persiapan', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
                ->get()
                ->groupBy('tanggal_persiapan');
        }

        $weeks = [];
        $currentDate = $startOfGrid->copy();
        $today = Carbon::now()->toDateString();

        while ($currentDate->lte($endOfGrid)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $currentDate->toDateString();
                $isCurrentMonth = $currentDate->month === $this->month;
                $isToday = ($dateStr === $today);
                $isSelected = ($dateStr === $this->selectedDate);

                $dayOrders = $orders->get($dateStr, collect());
                $dayPreps = $prepWorkOrders->get($dateStr, collect());

                $week[] = [
                    'date' => $dateStr,
                    'dayNumber' => $currentDate->day,
                    'isCurrentMonth' => $isCurrentMonth,
                    'isToday' => $isToday,
                    'isSelected' => $isSelected,
                    'dayName' => $currentDate->translatedFormat('l'),
                    'orders' => $dayOrders,
                    'preps' => $dayPreps,
                    'totalItems' => $dayOrders->count() + $dayPreps->count(),
                    'isPeak' => $dayOrders->count() >= 2,
                ];

                $currentDate->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * Weekly View Days
     */
    public function getWeekDaysProperty(): array
    {
        $baseDate = Carbon::createFromDate($this->year, $this->month, 1)->addWeeks($this->weekOffset);
        $startOfWeek = $baseDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $baseDate->copy()->endOfWeek(Carbon::SUNDAY);

        $orders = $this->getFilteredOrdersQuery()
            ->whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('jam', 'asc')
            ->get()
            ->groupBy('tanggal');

        $prepWorkOrders = collect();
        if ($this->showPrepDates) {
            $prepWorkOrders = WorkOrder::with(['order.customer', 'pic'])
                ->whereBetween('tanggal_persiapan', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->get()
                ->groupBy('tanggal_persiapan');
        }

        $days = [];
        $today = Carbon::now()->toDateString();
        $curr = $startOfWeek->copy();

        for ($i = 0; $i < 7; $i++) {
            $dateStr = $curr->toDateString();
            $dayOrders = $orders->get($dateStr, collect());
            $dayPreps = $prepWorkOrders->get($dateStr, collect());

            $days[] = [
                'date' => $dateStr,
                'dayNumber' => $curr->day,
                'dayName' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$i],
                'monthName' => $curr->format('M'),
                'isToday' => ($dateStr === $today),
                'isSelected' => ($dateStr === $this->selectedDate),
                'orders' => $dayOrders,
                'preps' => $dayPreps,
            ];
            $curr->addDay();
        }

        return $days;
    }

    /**
     * Agenda List of Orders for Active Period
     */
    public function getAgendaOrdersProperty(): Collection
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth()->toDateString();

        return $this->getFilteredOrdersQuery()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();
    }

    /**
     * Get details of currently selected order for modal view
     */
    public function getSelectedOrderDataProperty(): ?Order
    {
        if (!$this->selectedOrderId) {
            return null;
        }

        return Order::with([
            'customer',
            'items.product',
            'payments',
            'invoices',
            'workOrders.pic',
            'workOrders.users.user',
            'workOrders.equipment.inventory'
        ])->find($this->selectedOrderId);
    }

    /**
     * Get events happening on the selectedDate
     */
    public function getSelectedDateEventsProperty(): Collection
    {
        if (!$this->selectedDate) {
            return collect();
        }

        return $this->getFilteredOrdersQuery()
            ->where('tanggal', $this->selectedDate)
            ->orderBy('jam', 'asc')
            ->get();
    }
}
