<?php

namespace App\Filament\Resources\WorkOrders\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Penugasan Petugas / Tim';
    protected static ?string $modelLabel = 'Petugas';
    protected static ?string $pluralModelLabel = 'Petugas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Nama Petugas'),
                Forms\Components\TextInput::make('peran')
                    ->required()
                    ->default('petugas')
                    ->label('Peran / Tugas'),
                Forms\Components\Toggle::make('hadir')
                    ->label('Hadir di Event')
                    ->default(false),
                Forms\Components\TextInput::make('upah_dibayar')
                    ->label('Upah Aktual (Override)')
                    ->currencyIdr()
                    ->helperText('Kosongkan jika menggunakan upah standar dari profil karyawan'),
                Forms\Components\Textarea::make('catatan_kehadiran')
                    ->label('Catatan Kehadiran')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Petugas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peran')
                    ->label('Peran / Tugas')
                    ->searchable(),
                Tables\Columns\IconColumn::make('hadir')
                    ->label('Hadir')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('upah_dibayar')
                    ->label('Upah Event')
                    ->money('idr')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('catatan_kehadiran')
                    ->label('Catatan')
                    ->limit(20)
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('hadir')->label('Kehadiran'),
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
