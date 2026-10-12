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
        $this->app->bind(
            \Filament\Schemas\Components\StateCasts\StripCharactersStateCast::class,
            function ($app, array $params = []) {
                return new \App\Support\SafeStripCharactersStateCast($params['characters'] ?? []);
            }
        );
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
                ->formatStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    if (is_numeric($state)) {
                        return (int) round((float) $state);
                    }
                    return $state;
                })
                ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', ' . $precision . ')'))
                ->stripCharacters('.')
                ->numeric();
        });
    }
}
