<?php

namespace App\Filament\Resources;

use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\TagihanResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\DB;

class TagihanResource extends Resource
{
    protected static ?string $model = Order::class;
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tagihan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tagihan';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'KEUANGAN';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // You can add a filter to show only those with remaining balance, or all.
                // For now, let's show all and they can filter.
                $query->latest();
            })
            ->columns([
                TextColumn::make('no_order')->label('No. Order')->searchable(),
                TextColumn::make('customer.nama')->label('Pelanggan')->searchable(),
                TextColumn::make('nama_acara')->label('Acara'),
                TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('payments_sum_amount')
                    ->label('Sudah Dibayar')
                    ->sum('payments', 'amount')
                    ->money('idr'),
                TextColumn::make('sisa')
                    ->label('Sisa Tagihan')
                    ->state(function (Order $record): float {
                        return $record->total - $record->payments()->sum('amount');
                    })
                    ->money('idr')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->weight('bold'),
            ])
            ->actions([
                \Filament\Actions\Action::make('custom_invoice')
                    ->label('Invoice Custom (DP)')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('warning')
                    ->form([
                        TextInput::make('judul_tagihan')
                            ->label('Keterangan Tagihan')
                            ->required()
                            ->default('Pembayaran DP')
                            ->placeholder('Misal: Pembayaran DP 50%'),
                        TextInput::make('persentase')
                            ->label('Hitung dari Persentase (%)')
                            ->numeric()
                            ->suffix('%')
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($set, $state, Order $record) {
                                if ($state) {
                                    $set('nominal_tagihan', ($record->total * $state) / 100);
                                }
                            }),
                        TextInput::make('nominal_tagihan')
                            ->label('Nominal Tagihan (Rp)')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->default(fn (Order $record) => $record->total)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($set, $state, Order $record) {
                                if ($state && $record->total > 0) {
                                    $set('persentase', round(($state / $record->total) * 100, 2));
                                }
                            }),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->load(['customer', 'items.product', 'payments']);
                        $setting = \App\Models\Setting::first();
                        
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice_dynamic', [
                            'order' => $record,
                            'setting' => $setting,
                            'judul_tagihan' => $data['judul_tagihan'],
                            'nominal_tagihan' => $data['nominal_tagihan']
                        ]);
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Invoice-'.$data['judul_tagihan'].'-'.$record->no_order.'.pdf');
                    }),
                \Filament\Actions\Action::make('print_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Order $record) => route('invoice.download', $record))
                    ->openUrlInNewTab(),
                
                \Filament\Actions\Action::make('add_payment')
                    ->label('Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label('Jumlah Bayar')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (Order $record) => $record->total - $record->payments()->sum('amount')),
                        TextInput::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->default(now()),
                        TextInput::make('reference_number')
                            ->label('No. Referensi / Bukti')
                            ->default(fn () => 'PAY-' . strtoupper(uniqid())),
                        Textarea::make('notes')->label('Catatan'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->payments()->create($data);
                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran berhasil dicatat')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record) => ($record->total - $record->payments()->sum('amount')) > 0),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }
}
