<?php

namespace App\Providers;

use App\Support\SafeStripCharactersStateCast;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            StripCharactersStateCast::class,
            function ($app, array $params = []) {
                return new SafeStripCharactersStateCast($params['characters'] ?? []);
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Number::useLocale('id');
        Number::useCurrency('IDR');

        Table::configureUsing(function (Table $table) {
            $table->defaultNumberLocale('id')->defaultCurrency('idr');
        });

        Schema::configureUsing(function (Schema $schema) {
            $schema->defaultNumberLocale('id')->defaultCurrency('idr');
        });

        TextInput::macro('currencyIdr', function (string $prefix = 'Rp', int $precision = 0) {
            /** @var TextInput $this */
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
                ->mask(RawJs::make('$money($input, \',\', \'.\', '.$precision.')'))
                ->stripCharacters('.')
                ->numeric();
        });
    }
}
