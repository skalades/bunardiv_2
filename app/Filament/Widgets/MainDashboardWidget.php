<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class MainDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.main-dashboard-widget';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function getTodayOrdersProperty()
    {
        return Order::with('customer')
            ->whereDate('tanggal', Carbon::today())
            ->orderBy('jam', 'asc')
            ->get();
    }
}
