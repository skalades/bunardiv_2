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
        return $schema
            ->components([
                TextInput::make('potongan_lain')
                    ->label('Potongan Lain / Penyesuaian')
                    ->currencyIdr()
                    ->default(0)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($set, $state, $record) {
                        if ($record) {
                            $bersih = max(0, $record->total_bruto - $record->potongan_kasbon - (float) $state);
                            $set('total_bersih', $bersih);
                        }
                    }),
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
                    ->label('Metode Transfer')
                    ->options([
                        'Transfer BCA'     => 'Transfer BCA',
                        'Transfer Mandiri' => 'Transfer Mandiri',
                        'Transfer BRI'     => 'Transfer BRI',
                        'Tunai / Cash'     => 'Tunai / Cash',
                    ]),
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
                TextColumn::make('total_bruto')
                    ->label('Total Bruto')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('potongan_kasbon')
                    ->label('Potongan Kasbon')
                    ->money('idr')
                    ->color('danger')
                    ->placeholder('-'),
                TextColumn::make('potongan_lain')
                    ->label('Potongan Lain')
                    ->money('idr')
                    ->color('danger')
                    ->placeholder('-'),
                TextColumn::make('total_bersih')
                    ->label('Gaji Bersih')
                    ->money('idr')
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
                            ->options([
                                'Transfer BCA'     => 'Transfer BCA',
                                'Transfer Mandiri' => 'Transfer Mandiri',
                                'Transfer BRI'     => 'Transfer BRI',
                                'Tunai / Cash'     => 'Tunai / Cash',
                            ])
                            ->default('Transfer BCA')->required(),
                    ])
                    ->action(function (PayrollItem $record, array $data) {
                        $record->update([
                            'status'          => 'paid',
                            'tanggal_dibayar' => $data['tanggal_dibayar'],
                            'metode_bayar'    => $data['metode_bayar'],
                        ]);
                        Notification::make()->title('Gaji ditandai sudah dibayar')->success()->send();
                    }),
                EditAction::make(),
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
