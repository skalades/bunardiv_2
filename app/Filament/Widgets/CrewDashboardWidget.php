<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CrewDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.crew-dashboard-widget';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'crew';
    }
}
