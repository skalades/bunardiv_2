<?php

namespace App\Filament\Resources\InventoryTransactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InventoryTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_id')
                    ->relationship('inventory', 'nama')
                    ->label('Barang')
                    ->searchable()
                    ->required(),
                Select::make('order_id')
                    ->relationship('order', 'no_order')
                    ->label('Pesanan (Opsional)')
                    ->searchable(),
                Select::make('pic_id')
                    ->relationship('pic', 'name')
                    ->label('PIC (Penanggung Jawab)')
                    ->searchable(),
                Select::make('tipe')
                    ->options([
                        'keluar' => 'Keluar',
                        'masuk' => 'Masuk',
                        'opname' => 'Stok Opname',
                    ])
                    ->required(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('tanggal')
                    ->default(now())
                    ->required(),
                Select::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        'hilang' => 'Hilang',
                    ]),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }
}
