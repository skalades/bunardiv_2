<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kategori')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('satuan')
                    ->required()
                    ->default('pcs'),
                TextInput::make('stok_aktual')
                    ->numeric(),
            ]);
    }
}
