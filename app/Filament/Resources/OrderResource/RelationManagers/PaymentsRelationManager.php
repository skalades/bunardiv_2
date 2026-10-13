<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Invoice;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Pembayaran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->label('Invoice Terkait')
                    ->options(function ($livewire, $record) {
                        $order = $livewire->getOwnerRecord();
                        if (! $order) {
                            return [];
                        }
                        $order->loadMissing(['invoices', 'payments']);

                        $sisaOrder = max(0, $order->total - $order->payments()->sum('amount'));

                        $options = [];
                        foreach ($order->invoices as $inv) {
                            $paidForInv = Payment::where('invoice_id', $inv->id)
                                ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                                ->sum('amount');

                            $isMainInv = ($inv->no_invoice === 'INV-'.$order->no_order);
                            $sisaInv = $isMainInv
                                ? $sisaOrder
                                : max(0, $inv->nominal_tagihan - $paidForInv);

                            if ($sisaInv > 0 || ($record && $record->invoice_id === $inv->id)) {
                                $options[$inv->id] = "{$inv->no_invoice} - ".($inv->judul_tagihan ?: 'Tagihan').' (Sisa: Rp '.number_format($sisaInv, 0, ',', '.').')';
                            }
                        }

                        if (empty($options) && $sisaOrder > 0) {
                            $mainInv = $order->invoices()->firstOrCreate(
                                ['no_invoice' => 'INV-'.$order->no_order],
                                [
                                    'judul_tagihan' => 'Tagihan Pesanan '.$order->no_order,
                                    'nominal_tagihan' => $order->total,
                                    'jatuh_tempo' => $order->tanggal ?? now(),
                                ]
                            );
                            $options[$mainInv->id] = "{$mainInv->no_invoice} - {$mainInv->judul_tagihan} (Sisa: Rp ".number_format($sisaOrder, 0, ',', '.').')';
                        }

                        return $options;
                    })
                    ->default(function ($livewire) {
                        $order = $livewire->getOwnerRecord();
                        if (! $order) {
                            return null;
                        }
                        $order->loadMissing(['invoices', 'payments']);

                        $sisaOrder = max(0, $order->total - $order->payments()->sum('amount'));
                        if ($sisaOrder <= 0) {
                            return null;
                        }

                        $unpaidInv = $order->invoices->first(function ($inv) use ($order) {
                            if ($inv->no_invoice === 'INV-'.$order->no_order) {
                                return false;
                            }
                            $paid = Payment::where('invoice_id', $inv->id)->sum('amount');

                            return ($inv->nominal_tagihan - $paid) > 0;
                        });

                        if ($unpaidInv) {
                            return $unpaidInv->id;
                        }

                        $mainInv = $order->invoices->firstWhere('no_invoice', 'INV-'.$order->no_order);

                        return $mainInv?->id ?? $order->invoices->first()?->id;
                    })
                    ->live()
                    ->afterStateUpdated(function ($set, $state, $livewire) {
                        if ($state) {
                            $inv = Invoice::find($state);
                            if ($inv) {
                                $order = $livewire->getOwnerRecord();
                                $sisaOrder = $order ? max(0, $order->total - $order->payments()->sum('amount')) : 0;
                                $paidForInv = Payment::where('invoice_id', $inv->id)->sum('amount');
                                $isMainInv = ($order && $inv->no_invoice === 'INV-'.$order->no_order);
                                $sisaInv = $isMainInv ? $sisaOrder : max(0, $inv->nominal_tagihan - $paidForInv);
                                $targetAmount = $sisaInv > 0 ? $sisaInv : $sisaOrder;
                                $set('amount', (int) round($sisaOrder > 0 ? min($targetAmount, $sisaOrder) : $targetAmount));
                            }
                        }
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('Jumlah Bayar')
                    ->currencyIdr()
                    ->required()
                    ->default(function ($livewire, $get) {
                        $order = $livewire->getOwnerRecord();
                        if (! $order) {
                            return 0;
                        }
                        $order->loadMissing(['invoices', 'payments']);
                        $sisaOrder = max(0, $order->total - $order->payments()->sum('amount'));
                        if ($sisaOrder <= 0) {
                            return 0;
                        }

                        $invoiceId = $get('invoice_id');
                        $invoice = $invoiceId ? Invoice::find($invoiceId) : null;

                        if (! $invoice) {
                            $unpaidInv = $order->invoices->first(function ($inv) use ($order) {
                                if ($inv->no_invoice === 'INV-'.$order->no_order) {
                                    return false;
                                }
                                $paid = Payment::where('invoice_id', $inv->id)->sum('amount');

                                return ($inv->nominal_tagihan - $paid) > 0;
                            });
                            $invoice = $unpaidInv ?? $order->invoices->firstWhere('no_invoice', 'INV-'.$order->no_order);
                        }

                        if ($invoice) {
                            $paid = Payment::where('invoice_id', $invoice->id)->sum('amount');
                            $isMainInv = ($invoice->no_invoice === 'INV-'.$order->no_order);
                            $sisaInv = $isMainInv ? $sisaOrder : max(0, $invoice->nominal_tagihan - $paid);
                            if ($sisaInv > 0) {
                                return (int) round(min($sisaInv, $sisaOrder));
                            }
                        }

                        return (int) round($sisaOrder);
                    })
                    ->maxValue(fn ($livewire) => $livewire->getOwnerRecord() ? max(0, $livewire->getOwnerRecord()->total - $livewire->getOwnerRecord()->payments()->sum('amount')) : null)
                    ->minValue(1)
                    ->helperText(function ($get, $livewire) {
                        $order = $livewire->getOwnerRecord();
                        $sisaOrder = $order ? max(0, $order->total - $order->payments()->sum('amount')) : 0;
                        $invoiceId = $get('invoice_id');
                        if ($invoiceId && ($inv = Invoice::find($invoiceId))) {
                            $paid = Payment::where('invoice_id', $inv->id)->sum('amount');
                            $isMainInv = ($order && $inv->no_invoice === 'INV-'.$order->no_order);
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
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference_number')
            ->columns([
                TextColumn::make('payment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable(),
                TextColumn::make('invoice.no_invoice')
                    ->label('Invoice')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Pembayaran')
                    ->visible(fn ($livewire) => ($livewire->getOwnerRecord()?->total - $livewire->getOwnerRecord()?->payments()->sum('amount')) > 0),
            ])
            ->recordActions([
                Action::make('print')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('receipt.download', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
