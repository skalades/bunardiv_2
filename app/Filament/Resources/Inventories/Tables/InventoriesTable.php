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
                        return $record->stok_berjalan;
                    })
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->sortable(),
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
