<?php

namespace App\Filament\Resources\WorkOrders\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';
    protected static ?string $title = 'Peralatan';
    protected static ?string $modelLabel = 'Peralatan';
    protected static ?string $pluralModelLabel = 'Peralatan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('inventory_id')
                    ->relationship('inventory', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Nama Barang/Peralatan'),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('Jumlah'),
                Forms\Components\Select::make('status')
                    ->options([
                        'disiapkan' => 'Disiapkan',
                        'dipinjam' => 'Dipinjam / Digunakan',
                        'dikembalikan' => 'Dikembalikan',
                    ])
                    ->required()
                    ->default('disiapkan')
                    ->label('Status Barang'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inventory.nama')
            ->columns([
                Tables\Columns\TextColumn::make('inventory.nama')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disiapkan' => 'warning',
                        'dipinjam' => 'info',
                        'dikembalikan' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
