<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['customer'])->latest();
            })
            ->columns([
                TextColumn::make('no_order')->label('No. Order')->searchable(),
                TextColumn::make('customer.nama')->label('Pelanggan')->searchable(),
                TextColumn::make('nama_acara')->label('Acara'),
                TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('payments_sum_amount')
                    ->label('Sudah Dibayar')
                    ->sum('payments', 'amount')
                    ->money('idr', decimalPlaces: 0),
                TextColumn::make('sisa')
                    ->label('Sisa Tagihan')
                    ->state(function (Order $record): float {
                        $paid = $record->payments_sum_amount ?? $record->payments()->sum('amount');

                        return (float) ($record->total - $paid);
                    })
                    ->formatStateUsing(function (Order $record, $state): string {
                        if ($state < 0) {
                            return 'Rp 0 (Lebih Bayar Rp '.number_format(abs($state), 0, ',', '.').')';
                        }
                        if ($state == 0) {
                            return 'Rp 0 (LUNAS)';
                        }

                        return 'Rp '.number_format($state, 0, ',', '.');
                    })
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->weight('bold'),
            ])
            ->actions([
                Action::make('custom_invoice')
                    ->label('Invoice Custom')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('warning')
                    ->form([
                        TextInput::make('judul_tagihan')
                            ->label('Keterangan Tagihan')
                            ->required()
                            ->default(function (Order $record) {
                                $paid = $record->payments()->sum('amount');

                                return $paid > 0 ? 'Pelunasan Tagihan' : 'Pembayaran DP';
                            })
                            ->placeholder('Misal: Pembayaran DP 50% atau Pelunasan'),
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
                            ->default(function (Order $record) {
                                $paid = $record->payments()->sum('amount');
                                $sisa = max(0, $record->total - $paid);

                                return (int) round($sisa > 0 ? $sisa : $record->total);
                            })
                            ->helperText(function (Order $record) {
                                $paid = $record->payments()->sum('amount');
                                $sisa = max(0, $record->total - $paid);

                                return 'Sisa belum terbayar: Rp '.number_format($sisa, 0, ',', '.').' (Total order: Rp '.number_format($record->total, 0, ',', '.').')';
                            })
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($set, $state, Order $record) {
                                if ($state && $record->total > 0) {
                                    $set('persentase', round(($state / $record->total) * 100, 2));
                                }
                            }),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->load(['customer', 'items.product', 'payments', 'invoices']);
                        $setting = Setting::getSettings();

                        $count = $record->invoices()->count() + 1;
                        $noInvoice = 'INV-'.$record->no_order.'-DP'.$count;
                        $record->invoices()->create([
                            'no_invoice' => $noInvoice,
                            'judul_tagihan' => $data['judul_tagihan'],
                            'nominal_tagihan' => $data['nominal_tagihan'],
                            'jatuh_tempo' => now()->addDays(7),
                        ]);

                        $pdf = Pdf::loadView('pdf.invoice_dynamic', [
                            'order' => $record,
                            'setting' => $setting,
                            'judul_tagihan' => $data['judul_tagihan'],
                            'nominal_tagihan' => $data['nominal_tagihan'],
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Invoice-'.$data['judul_tagihan'].'-'.$record->no_order.'.pdf');
                    }),
                Action::make('print_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(function (Order $record) {
                        $record->invoices()->firstOrCreate(
                            ['no_invoice' => 'INV-'.$record->no_order],
                            [
                                'judul_tagihan' => 'Tagihan Pesanan '.$record->no_order,
                                'nominal_tagihan' => $record->total,
                                'jatuh_tempo' => $record->tanggal ?? now(),
                            ]
                        );

                        return route('invoice.download', $record);
                    })
                    ->openUrlInNewTab(),

                Action::make('add_payment')
                    ->label('Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
                        Select::make('invoice_id')
                            ->label('Invoice Terkait')
                            ->options(function (Order $record) {
                                $record->loadMissing(['invoices', 'payments']);
                                $sisaOrder = max(0, $record->total - $record->payments()->sum('amount'));

                                $options = [];
                                foreach ($record->invoices as $inv) {
                                    $paidForInv = Payment::where('invoice_id', $inv->id)->sum('amount');
                                    $isMainInv = ($inv->no_invoice === 'INV-'.$record->no_order);
                                    $sisaInv = $isMainInv
                                        ? $sisaOrder
                                        : max(0, $inv->nominal_tagihan - $paidForInv);

                                    if ($sisaInv > 0) {
                                        $options[$inv->id] = "{$inv->no_invoice} - ".($inv->judul_tagihan ?: 'Tagihan').' (Sisa: Rp '.number_format($sisaInv, 0, ',', '.').')';
                                    }
                                }

                                if (empty($options) && $sisaOrder > 0) {
                                    $mainInv = $record->invoices()->firstOrCreate(
                                        ['no_invoice' => 'INV-'.$record->no_order],
                                        [
                                            'judul_tagihan' => 'Tagihan Pesanan '.$record->no_order,
                                            'nominal_tagihan' => $record->total,
                                            'jatuh_tempo' => $record->tanggal ?? now(),
                                        ]
                                    );
                                    $options[$mainInv->id] = "{$mainInv->no_invoice} - {$mainInv->judul_tagihan} (Sisa: Rp ".number_format($sisaOrder, 0, ',', '.').')';
                                }

                                return $options;
                            })
                            ->default(function (Order $record) {
                                $record->loadMissing(['invoices', 'payments']);
                                $sisaOrder = max(0, $record->total - $record->payments()->sum('amount'));
                                if ($sisaOrder <= 0) {
                                    return null;
                                }

                                $unpaidInv = $record->invoices->first(function ($inv) use ($record) {
                                    if ($inv->no_invoice === 'INV-'.$record->no_order) {
                                        return false;
                                    }
                                    $paid = Payment::where('invoice_id', $inv->id)->sum('amount');

                                    return ($inv->nominal_tagihan - $paid) > 0;
                                });

                                if ($unpaidInv) {
                                    return $unpaidInv->id;
                                }

                                $mainInv = $record->invoices->firstWhere('no_invoice', 'INV-'.$record->no_order);

                                return $mainInv?->id ?? $record->invoices->first()?->id;
                            })
                            ->live()
                            ->afterStateUpdated(function ($set, $state, Order $record) {
                                if ($state) {
                                    $inv = Invoice::find($state);
                                    if ($inv) {
                                        $sisaOrder = max(0, $record->total - $record->payments()->sum('amount'));
                                        $paidForInv = Payment::where('invoice_id', $inv->id)->sum('amount');
                                        $isMainInv = ($inv->no_invoice === 'INV-'.$record->no_order);
                                        $sisaInv = $isMainInv ? $sisaOrder : max(0, $inv->nominal_tagihan - $paidForInv);
                                        $targetAmount = $sisaInv > 0 ? $sisaInv : $sisaOrder;
                                        $set('amount', (int) round($sisaOrder > 0 ? min($targetAmount, $sisaOrder) : $targetAmount));
                                    }
                                }
                            })
                            ->required()
                            ->searchable(),
                        TextInput::make('amount')
                            ->label('Jumlah Bayar')
                            ->currencyIdr()
                            ->required()
                            ->default(function (Order $record, $get) {
                                $record->loadMissing(['invoices', 'payments']);
                                $sisaOrder = max(0, $record->total - $record->payments()->sum('amount'));
                                if ($sisaOrder <= 0) {
                                    return 0;
                                }

                                $invoiceId = $get('invoice_id');
                                $invoice = $invoiceId ? Invoice::find($invoiceId) : null;

                                if (! $invoice) {
                                    $unpaidInv = $record->invoices->first(function ($inv) use ($record) {
                                        if ($inv->no_invoice === 'INV-'.$record->no_order) {
                                            return false;
                                        }
                                        $paid = Payment::where('invoice_id', $inv->id)->sum('amount');

                                        return ($inv->nominal_tagihan - $paid) > 0;
                                    });
                                    $invoice = $unpaidInv ?? $record->invoices->firstWhere('no_invoice', 'INV-'.$record->no_order);
                                }

                                if ($invoice) {
                                    $paid = Payment::where('invoice_id', $invoice->id)->sum('amount');
                                    $isMainInv = ($invoice->no_invoice === 'INV-'.$record->no_order);
                                    $sisaInv = $isMainInv ? $sisaOrder : max(0, $invoice->nominal_tagihan - $paid);
                                    if ($sisaInv > 0) {
                                        return (int) round(min($sisaInv, $sisaOrder));
                                    }
                                }

                                return (int) round($sisaOrder);
                            })
                            ->maxValue(fn (Order $record) => max(0, $record->total - $record->payments()->sum('amount')))
                            ->minValue(1)
                            ->helperText(function ($get, Order $record) {
                                $sisaOrder = max(0, $record->total - $record->payments()->sum('amount'));
                                $invoiceId = $get('invoice_id');
                                if ($invoiceId && ($inv = Invoice::find($invoiceId))) {
                                    $paid = Payment::where('invoice_id', $inv->id)->sum('amount');
                                    $isMainInv = ($inv->no_invoice === 'INV-'.$record->no_order);
                                    $sisaInv = $isMainInv ? $sisaOrder : max(0, $inv->nominal_tagihan - $paid);
                                    if ($sisaInv <= 0) {
                                        return 'Invoice ini sudah LUNAS. (Sisa tagihan order: Rp '.number_format($sisaOrder, 0, ',', '.').')';
                                    }

                                    return 'Sisa tagihan invoice ini: Rp '.number_format($sisaInv, 0, ',', '.').' (Total order sisa: Rp '.number_format($sisaOrder, 0, ',', '.').')';
                                }

                                return 'Sisa tagihan order: Rp '.number_format($sisaOrder, 0, ',', '.');
                            }),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options(fn () => Payment::getPaymentMethods())
                            ->default('Transfer BCA')
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->default(now()),
                        TextInput::make('reference_number')
                            ->label('No. Referensi / Bukti')
                            ->default(fn () => 'PAY-'.strtoupper(uniqid())),
                        Textarea::make('notes')->label('Catatan'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->payments()->create($data);
                        Notification::make()
                            ->title('Pembayaran berhasil dicatat')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Order $record) => ($record->total - ($record->payments_sum_amount ?? $record->payments()->sum('amount'))) > 0),
                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading(fn (Order $record) => "Hapus Pesanan {$record->no_order}")
                    ->modalDescription('Apakah Anda yakin ingin menghapus pesanan ini beserta seluruh tagihannya? Semua rincian invoice dan pembayaran terkait akan dihapus secara permanen.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
        ];
    }
}
