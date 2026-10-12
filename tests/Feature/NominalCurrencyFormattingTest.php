<?php

namespace Tests\Feature;

use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\TagihanResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Livewire\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Tests\TestCase;

class NominalCurrencyFormattingTest extends TestCase
{
    public function test_currency_idr_macro_is_registered_and_configured(): void
    {
        $input = TextInput::make('nominal_tagihan')->currencyIdr();

        $this->assertEquals('Rp', $input->getPrefixLabel());
        $this->assertTrue($input->isNumeric());
        $this->assertEquals(['.'], $input->getStripCharacters());
        $this->assertNotNull($input->getMask());
    }

    public function test_masked_currency_string_is_properly_stripped_and_cast_to_numeric(): void
    {
        $livewire = new class extends Component implements HasSchemas {
            use InteractsWithSchemas;
            public ?array $data = [];
            public function render() { return '<div></div>'; }
        };

        $schema = Schema::make($livewire)
            ->statePath('data')
            ->components([
                TextInput::make('nominal_tagihan')->currencyIdr()->required(),
                TextInput::make('diskon')->currencyIdr(),
            ]);

        // Simulating user input containing formatted Indonesian Rupiah with thousand separators
        $livewire->data['nominal_tagihan'] = '425.437.606';
        $livewire->data['diskon'] = '50.000';

        $state = $schema->getState();

        $this->assertEquals(425437606.0, $state['nominal_tagihan']);
        $this->assertEquals(50000.0, $state['diskon']);
    }

    public function test_zero_and_null_values_are_handled_correctly(): void
    {
        $livewire = new class extends Component implements HasSchemas {
            use InteractsWithSchemas;
            public ?array $data = [];
            public function render() { return '<div></div>'; }
        };

        $schema = Schema::make($livewire)
            ->statePath('data')
            ->components([
                TextInput::make('diskon')->currencyIdr(),
                TextInput::make('biaya_tambahan')->currencyIdr(),
            ]);

        $livewire->data['diskon'] = '0';
        $livewire->data['biaya_tambahan'] = '';

        $state = $schema->getState();

        $this->assertEquals(0.0, $state['diskon']);
        $this->assertNull($state['biaya_tambahan']);
    }

    public function test_invoice_resource_nominal_field_uses_currency_idr(): void
    {
        $livewire = new class extends Component implements HasSchemas {
            use InteractsWithSchemas;
            public function render() { return '<div></div>'; }
        };

        $schema = InvoiceResource::form(Schema::make($livewire));
        $components = $schema->getFlatComponents();
        
        $nominalField = collect($components)->first(fn ($c) => $c instanceof TextInput && $c->getName() === 'nominal_tagihan');
        $this->assertNotNull($nominalField);
        $this->assertEquals('Rp', $nominalField->getPrefixLabel());
        $this->assertEquals(['.'], $nominalField->getStripCharacters());
    }

    public function test_currency_idr_formats_decimal_database_values_without_multiplying_by_100(): void
    {
        $livewire = new class extends Component implements HasSchemas {
            use InteractsWithSchemas;
            public ?array $data = [];
            public function render() { return '<div></div>'; }
        };

        $schema = Schema::make($livewire)
            ->statePath('data')
            ->components([
                TextInput::make('jumlah_bayar')->currencyIdr(),
            ]);

        // When database provides '500000.00' (decimal:2 cast string)
        $schema->fill(['jumlah_bayar' => '500000.00']);
        $this->assertEquals(500000, $livewire->data['jumlah_bayar']);

        $schema->fill(['jumlah_bayar' => 500000.00]);
        $this->assertEquals(500000, $livewire->data['jumlah_bayar']);

        $schema->fill(['jumlah_bayar' => '500000']);
        $this->assertEquals(500000, $livewire->data['jumlah_bayar']);

        $schema->fill(['jumlah_bayar' => null]);
        $this->assertNull($livewire->data['jumlah_bayar']);
    }
}
