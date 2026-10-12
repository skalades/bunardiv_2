<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Filament\Forms\Components\TextInput::macro('currencyIdr', function (string $prefix = 'Rp', int $precision = 0) {
            /** @var \Filament\Forms\Components\TextInput $this */
            return $this
                ->prefix($prefix)
                ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', ' . $precision . ')'))
                ->stripCharacters('.')
                ->numeric();
        });
    }
}
