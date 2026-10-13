<?php

namespace App\Filament\Resources\PayrollResource\RelationManagers;

use App\Models\PayrollItem;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Rincian Slip Gaji Karyawan';

    protected static ?string $modelLabel = 'Slip Gaji';

    protected static ?string $pluralModelLabel = 'Rincian Penggajian';

    public function form(Schema $schema): Schema
    {
        $recalculate = function ($get, $set, $record) {
            if (! $record) {
                return;
            }

            $bonus        = (float) ($get('bonus') ?? 0);
            $lembur       = (float) ($get('lembur') ?? 0);
            $potonganLain = (float) ($get('potongan_lain') ?? 0);

            $baseBruto = (float) $record->total_bruto - (float) ($record->bonus ?? 0) - (float) ($record->lembur ?? 0);
            $newBruto  = max(0, $baseBruto + $bonus + $lembur);
            $newBersih = max(0, $newBruto - (float) $record->potongan_kasbon - $potonganLain);

            $set('total_bruto', $newBruto);
            $set('total_bersih', $newBersih);
        };

        return $schema
            ->components([
                TextInput::make('bonus')
                    ->label('Bonus')
                    ->currencyIdr()
                    ->default(0)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($get, $set, $record) => $recalculate($get, $set, $record)),
                TextInput::make('lembur')
                    ->label('Uang Lembur')
                    ->currencyIdr()
                    ->default(0)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($get, $set, $record) => $recalculate($get, $set, $record)),
                TextInput::make('potongan_lain')
                    ->label('Potongan Lain / Penyesuaian')
                    ->currencyIdr()
                    ->default(0)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($get, $set, $record) => $recalculate($get, $set, $record)),
                TextInput::make('total_bersih')
                    ->label('Total Gaji Bersih')
                    ->currencyIdr()
                    ->disabled()
                    ->dehydrated(),
                Select::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'draft' => 'Draft / Belum Ditransfer',
                        'paid'  => 'Sudah Dibayarkan (Lunas)',
                    ])
                    ->required(),
                DatePicker::make('tanggal_dibayar')
                    ->label('Tanggal Dibayar')
                    ->default(now()),
                Select::make('metode_bayar')
                    ->label('Metode Pembayaran')
                    ->options(fn () => \App\Models\Payment::getPaymentMethods())
                    ->default('Transfer BCA'),
                Textarea::make('catatan')
                    ->label('Catatan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('tipe_karyawan')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state === 'bulanan' ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('kehadiran')
                    ->label('Kehadiran / Event')
                    ->getStateUsing(function (PayrollItem $record) {
                        return $record->tipe_karyawan === 'bulanan'
                            ? ($record->jumlah_hari_hadir . ' Hari')
                            : ($record->jumlah_event_hadir . ' Event/SPK');
                    }),
                TextColumn::make('bonus')
                    ->label('Bonus')
                    ->money('idr', decimalPlaces: 0)
                    ->color('success')
                    ->placeholder('-'),
                TextColumn::make('lembur')
                    ->label('Lembur')
                    ->money('idr', decimalPlaces: 0)
                    ->color('success')
                    ->placeholder('-'),
                TextColumn::make('total_bruto')
                    ->label('Total Bruto')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('potongan_kasbon')
                    ->label('Potongan Kasbon')
                    ->money('idr', decimalPlaces: 0)
                    ->color('danger')
                    ->placeholder('-'),
                TextColumn::make('potongan_lain')
                    ->label('Potongan Lain')
                    ->money('idr', decimalPlaces: 0)
                    ->color('danger')
                    ->placeholder('-'),
                TextColumn::make('total_bersih')
                    ->label('Gaji Bersih')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state === 'paid' ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === 'paid' ? 'Lunas' : 'Draft'),
            ])
            ->actions([
                Action::make('cetak_slip')
                    ->label('Slip Gaji')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (PayrollItem $record) => route('payroll.slip', $record))
                    ->openUrlInNewTab(),
                Action::make('tandai_lunas')
                    ->label('Bayar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PayrollItem $record) => $record->status !== 'paid')
                    ->form([
                        DatePicker::make('tanggal_dibayar')->label('Tgl Bayar')->default(now())->required(),
                        Select::make('metode_bayar')
                            ->label('Metode Pembayaran')
                            ->options(fn () => \App\Models\Payment::getPaymentMethods())
                            ->default('Transfer BCA')
                            ->required(),
                    ])
                    ->action(function (PayrollItem $record, array $data) {
                        $record->update([
                            'status'          => 'paid',
                            'tanggal_dibayar' => $data['tanggal_dibayar'],
                            'metode_bayar'    => $data['metode_bayar'],
                        ]);
                        Notification::make()->title('Gaji ditandai sudah dibayar')->success()->send();
                    }),
                EditAction::make()
                    ->using(function (PayrollItem $record, array $data): PayrollItem {
                        $baseBruto            = (float) $record->total_bruto - (float) ($record->bonus ?? 0) - (float) ($record->lembur ?? 0);
                        $bonus                = (float) ($data['bonus'] ?? 0);
                        $lembur               = (float) ($data['lembur'] ?? 0);
                        $potonganLain         = (float) ($data['potongan_lain'] ?? 0);
                        $data['total_bruto']  = max(0, $baseBruto + $bonus + $lembur);
                        $data['total_bersih'] = max(0, $data['total_bruto'] - (float) $record->potongan_kasbon - $potonganLain);

                        $record->update($data);

                        return $record;
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_all_paid')
                        ->label('Tandai Semua Lunas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update([
                                'status'          => 'paid',
                                'tanggal_dibayar' => now()->toDateString(),
                                'metode_bayar'    => 'Transfer Bank',
                            ]);
                            Notification::make()->title('Semua gaji terpilih ditandai lunas')->success()->send();
                        }),
                ]),
            ]);
    }
}
