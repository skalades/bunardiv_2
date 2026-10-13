<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategori')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('satuan')
                    ->searchable(),
                TextColumn::make('stok_aktual')
                    ->label('Stok Opname (Aktual)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stok_berjalan')
                    ->label('Stok Berjalan (Estimasi)')
                    ->getStateUsing(function ($record) {
                        if (is_null($record->stok_aktual)) {
                            return null;
                        }
                        $masuk = $record->total_masuk ?? $record->transactions()->where('tipe', 'masuk')->sum('qty');
                        $keluar = $record->total_keluar ?? $record->transactions()->where('tipe', 'keluar')->sum('qty');

                        return $record->stok_aktual + $masuk - $keluar;
                    })
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
