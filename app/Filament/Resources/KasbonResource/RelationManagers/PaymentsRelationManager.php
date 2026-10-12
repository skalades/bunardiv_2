<?php

namespace App\Filament\Resources\KasbonResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Riwayat Cicilan & Pembayaran';

    protected static ?string $modelLabel = 'Cicilan';

    protected static ?string $pluralModelLabel = 'Riwayat Cicilan';

    public function form(Schema $schema): Schema
    {
        $loan = $this->getOwnerRecord();

        return $schema
            ->components([
                TextInput::make('jumlah_bayar')
                    ->label('Nominal Pembayaran')
                    ->currencyIdr()
                    ->required()
                    ->default(fn () => (int) round($loan->sisa_hutang))
                    ->helperText('Maksimal: Rp ' . number_format($loan->sisa_hutang, 0, ',', '.')),
                DatePicker::make('tanggal')
                    ->label('Tanggal Bayar')
                    ->default(now())
                    ->required(),
                Select::make('sumber')
                    ->label('Metode / Sumber')
                    ->options([
                        'manual'      => 'Pembayaran Langsung (Tunai / Transfer)',
                        'potong_gaji' => 'Potong Gaji (Payroll)',
                    ])
                    ->default('manual')
                    ->required(),
                Textarea::make('catatan')
                    ->label('Catatan Bukti / Keterangan')
                    ->placeholder('Contoh: Transfer BCA cicilan ke-1')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jumlah_bayar')
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('sumber')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn ($state) => $state === 'potong_gaji' ? 'info' : 'success')
                    ->formatStateUsing(fn ($state) => $state === 'potong_gaji' ? 'Potong Gaji' : 'Manual'),
                TextColumn::make('pencatat.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('-'),
                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->limit(30),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat Cicilan')
                    ->visible(fn () => $this->getOwnerRecord()->sisa_hutang > 0 && $this->getOwnerRecord()->status === 'disetujui')
                    ->mutateFormDataUsing(function (array $data): array {
                        $loan = $this->getOwnerRecord();
                        $bayar = min((float) $data['jumlah_bayar'], (float) $loan->sisa_hutang);
                        $data['jumlah_bayar'] = $bayar;
                        $data['dicatat_oleh'] = auth()->id();

                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Pembayaran cicilan berhasil dicatat')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
