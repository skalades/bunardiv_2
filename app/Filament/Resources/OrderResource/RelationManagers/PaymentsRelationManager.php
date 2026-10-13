<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Invoice;
use App\Models\Payment;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                    ->options(function ($livewire) {
                        $order = $livewire->getOwnerRecord();
                        if (! $order) return [];
                        $order->loadMissing('invoices');
                        if ($order->invoices->isEmpty()) {
                            $mainInv = $order->invoices()->firstOrCreate(
                                ['no_invoice' => 'INV-' . $order->no_order],
                                [
                                    'judul_tagihan'   => 'Tagihan Pesanan ' . $order->no_order,
                                    'nominal_tagihan' => $order->total,
                                    'jatuh_tempo'     => $order->tanggal ?? now(),
                                ]
                            );
                            return [$mainInv->id => "{$mainInv->no_invoice} - {$mainInv->judul_tagihan} (Rp " . number_format($mainInv->nominal_tagihan, 0, ',', '.') . ")"];
                        }
                        return $order->invoices->mapWithKeys(fn ($inv) => [
                            $inv->id => "{$inv->no_invoice} - " . ($inv->judul_tagihan ?: 'Tagihan') . " (Rp " . number_format($inv->nominal_tagihan, 0, ',', '.') . ")"
                        ]);
                    })
                    ->default(function ($livewire) {
                        $order = $livewire->getOwnerRecord();
                        if (! $order) return null;
                        $order->loadMissing('invoices');
                        $unpaidInv = $order->invoices->first(function ($inv) {
                            $paid = \App\Models\Payment::where('invoice_id', $inv->id)->sum('amount');
                            return ($inv->nominal_tagihan - $paid) > 0;
                        });
                        return $unpaidInv?->id ?? $order->invoices->last()?->id;
                    })
                    ->live()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $inv = Invoice::find($state);
                            if ($inv) {
                                $paidForInv = \App\Models\Payment::where('invoice_id', $inv->id)->sum('amount');
                                $sisaInv = max(0, $inv->nominal_tagihan - $paidForInv);
                                $set('amount', (int) round($sisaInv > 0 ? $sisaInv : $inv->nominal_tagihan));
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
                        if (! $order) return 0;
                        $order->loadMissing('invoices');
                        $invoiceId = $get('invoice_id');
                        if (! $invoiceId) {
                            $unpaidInv = $order->invoices->first(function ($inv) {
                                $paid = \App\Models\Payment::where('invoice_id', $inv->id)->sum('amount');
                                return ($inv->nominal_tagihan - $paid) > 0;
                            });
                            $invoice = $unpaidInv ?? $order->invoices->last();
                        } else {
                            $invoice = Invoice::find($invoiceId);
                        }

                        if ($invoice) {
                            $paid = \App\Models\Payment::where('invoice_id', $invoice->id)->sum('amount');
                            $sisaInv = max(0, $invoice->nominal_tagihan - $paid);
                            if ($sisaInv > 0) {
                                return (int) round($sisaInv);
                            }
                        }

                        return (int) round(max(0, $order->total - $order->payments()->sum('amount')));
                    })
                    ->helperText(function ($get, $livewire) {
                        $invoiceId = $get('invoice_id');
                        if ($invoiceId && ($inv = Invoice::find($invoiceId))) {
                            $paid = \App\Models\Payment::where('invoice_id', $inv->id)->sum('amount');
                            $sisa = max(0, $inv->nominal_tagihan - $paid);
                            return "Nominal invoice ini: Rp " . number_format($inv->nominal_tagihan, 0, ',', '.') . " (Sisa tagihan invoice: Rp " . number_format($sisa, 0, ',', '.') . ")";
                        }
                        $order = $livewire->getOwnerRecord();
                        if ($order) {
                            $sisaOrder = max(0, $order->total - $order->payments()->sum('amount'));
                            return "Sisa tagihan order: Rp " . number_format($sisaOrder, 0, ',', '.');
                        }
                        return null;
                    }),
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
                CreateAction::make()->label('Tambah Pembayaran'),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('print')
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
