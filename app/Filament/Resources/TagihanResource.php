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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class TagihanResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

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
                                    $set('nominal_tagihan', round(($record->total * $state) / 100));
                                }
                            }),
                        TextInput::make('nominal_tagihan')
                            ->label('Nominal Tagihan')
                            ->currencyIdr()
                            ->required()
                            ->default(fn (Order $record) => $record->total)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($set, $state, Order $record) {
                                if ($state && $record->total > 0) {
                                    $set('persentase', round(($state / $record->total) * 100, 2));
                                }
                            }),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->load(['customer', 'items.product', 'payments', 'invoices']);
                        $setting = \App\Models\Setting::first();
                        
                        $count = $record->invoices()->count() + 1;
                        $noInvoice = 'INV-' . $record->no_order . '-DP' . $count;
                        $record->invoices()->create([
                            'no_invoice'      => $noInvoice,
                            'judul_tagihan'   => $data['judul_tagihan'],
                            'nominal_tagihan' => $data['nominal_tagihan'],
                            'jatuh_tempo'     => now()->addDays(7),
                        ]);

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice_dynamic', [
                            'order' => $record,
                            'setting' => $setting,
                            'judul_tagihan' => $data['judul_tagihan'],
                            'nominal_tagihan' => $data['nominal_tagihan']
                        ])->setPaper('a4', 'portrait');
                        
                        return response()->streamDownload(function () use ($pdf, $data, $record) {
                            echo $pdf->output();
                        }, 'Invoice-'.$data['judul_tagihan'].'-'.$record->no_order.'.pdf');
                    }),
                \Filament\Actions\Action::make('print_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(function (Order $record) {
                        $record->invoices()->firstOrCreate(
                            ['no_invoice' => 'INV-' . $record->no_order],
                            [
                                'judul_tagihan'   => 'Tagihan Pesanan ' . $record->no_order,
                                'nominal_tagihan' => $record->total,
                                'jatuh_tempo'     => $record->tanggal ?? now(),
                            ]
                        );
                        return route('invoice.download', $record);
                    })
                    ->openUrlInNewTab(),
                
                \Filament\Actions\Action::make('add_payment')
                    ->label('Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
                        Select::make('invoice_id')
                            ->label('Invoice Terkait')
                            ->options(function (Order $record) {
                                $record->loadMissing('invoices');
                                if ($record->invoices->isEmpty()) {
                                    $mainInv = $record->invoices()->firstOrCreate(
                                        ['no_invoice' => 'INV-' . $record->no_order],
                                        [
                                            'judul_tagihan'   => 'Tagihan Pesanan ' . $record->no_order,
                                            'nominal_tagihan' => $record->total,
                                            'jatuh_tempo'     => $record->tanggal ?? now(),
                                        ]
                                    );
                                    return [$mainInv->id => "{$mainInv->no_invoice} - {$mainInv->judul_tagihan} (Rp " . number_format($mainInv->nominal_tagihan, 0, ',', '.') . ")"];
                                }

                                return $record->invoices->mapWithKeys(function ($inv) {
                                    return [$inv->id => "{$inv->no_invoice} - " . ($inv->judul_tagihan ?: 'Tagihan') . " (Rp " . number_format($inv->nominal_tagihan, 0, ',', '.') . ")"];
                                });
                            })
                            ->default(function (Order $record) {
                                $record->loadMissing('invoices');
                                if ($record->invoices->isEmpty()) {
                                    $mainInv = $record->invoices()->firstOrCreate(
                                        ['no_invoice' => 'INV-' . $record->no_order],
                                        [
                                            'judul_tagihan'   => 'Tagihan Pesanan ' . $record->no_order,
                                            'nominal_tagihan' => $record->total,
                                            'jatuh_tempo'     => $record->tanggal ?? now(),
                                        ]
                                    );
                                    return $mainInv->id;
                                }
                                return $record->invoices->last()?->id;
                            })
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $inv = Invoice::find($state);
                                    if ($inv) {
                                        $paidForInv = \App\Models\Payment::where('invoice_id', $inv->id)->sum('amount');
                                        $sisaInv = max(0, $inv->nominal_tagihan - $paidForInv);
                                        if ($sisaInv > 0) {
                                            $set('amount', round($sisaInv));
                                        }
                                    }
                                }
                            })
                            ->required()
                            ->searchable(),
                        TextInput::make('amount')
                            ->label('Jumlah Bayar')
                            ->currencyIdr()
                            ->required()
                            ->default(fn (Order $record) => round($record->total - $record->payments()->sum('amount'))),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Transfer BCA'     => 'Transfer BCA',
                                'Transfer Mandiri' => 'Transfer Mandiri',
                                'Transfer BRI'     => 'Transfer BRI',
                                'Tunai / Cash'     => 'Tunai / Cash',
                                'QRIS / E-Wallet'  => 'QRIS / E-Wallet',
                                'Kartu Kredit'     => 'Kartu Kredit',
                                'Lainnya'          => 'Lainnya',
                            ])
                            ->default('Transfer BCA')
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
