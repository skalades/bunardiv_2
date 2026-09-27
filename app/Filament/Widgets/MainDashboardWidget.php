<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MainDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.main-dashboard-widget';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
