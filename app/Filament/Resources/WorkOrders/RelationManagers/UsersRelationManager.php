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
                    ->money('idr', decimalPlaces: 0)
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
                \Filament\Actions\Action::make('mark_all_present')
                    ->label('Tandai Semua Hadir')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Semua Petugas Hadir')
                    ->modalDescription('Apakah Anda yakin ingin menandai seluruh petugas yang ditugaskan pada SPK ini sebagai Hadir?')
                    ->action(function () {
                        $wo = $this->getOwnerRecord();
                        $wo->users()->update([
                            'hadir'             => true,
                            'waktu_absen'       => now(),
                            'catatan_kehadiran' => 'Presensi massal oleh Admin pada ' . now()->format('d M Y H:i') . ' WIB',
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Semua petugas berhasil ditandai Hadir.')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\CreateAction::make()->label('Tambah Petugas'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('set_hadir_crew')
                        ->label('Tandai Hadir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update([
                                'hadir'             => true,
                                'waktu_absen'       => now(),
                                'catatan_kehadiran' => 'Presensi massal oleh Admin pada ' . now()->format('d M Y H:i') . ' WIB',
                            ]);
                            \Filament\Notifications\Notification::make()->title('Petugas terpilih ditandai Hadir')->success()->send();
                        }),
                    \Filament\Actions\BulkAction::make('set_tidak_hadir_crew')
                        ->label('Tandai Tidak Hadir')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update([
                                'hadir'             => false,
                                'waktu_absen'       => null,
                                'catatan_kehadiran' => null,
                            ]);
                            \Filament\Notifications\Notification::make()->title('Petugas terpilih ditandai Tidak Hadir')->warning()->send();
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
