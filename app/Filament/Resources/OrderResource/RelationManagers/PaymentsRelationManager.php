<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
                TextInput::make('amount')
                    ->label('Jumlah Bayar')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('payment_method')
                    ->label('Metode Pembayaran (Cash, Transfer, dll)')
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
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('idr')
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
