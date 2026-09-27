<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shopping-bag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'MANAJEMEN ORDER';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pesanan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pesanan';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // POS Menu Grid (Left Side)
                Section::make('Pilih Menu')
                    ->schema([
                        ViewField::make('pos_menu')
                            ->view('filament.forms.components.pos-menu')
                            ->columnSpanFull()
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                
                // POS Form (Right Side)
                Section::make('Detail Pesanan')
                    ->schema([
                        Select::make('customer_id')
                            ->options(function () {
                                $customers = \App\Models\Customer::pluck('nama', 'id')->toArray();
                                return ['new' => '+ Tambah pelanggan baru'] + $customers;
                            })
                            ->searchable()
                            ->live()
                            ->label('PELANGGAN'),

                        Section::make()
                            ->schema([
                                TextInput::make('new_customer_nama')
                                    ->required(fn ($get) => $get('customer_id') === 'new')
                                    ->label('Nama'),
                                TextInput::make('new_customer_no_wa')
                                    ->tel()
                                    ->label('No. WA'),
                                TextInput::make('new_customer_alamat')
                                    ->label('Alamat'),
                            ])
                            ->visible(fn ($get) => $get('customer_id') === 'new')
                            ->extraAttributes(['class' => 'bg-white/5 border border-white/10 rounded-xl p-4']),

                        TextInput::make('nama_acara')
                            ->required()
                            ->label('Nama Acara'),
                        Select::make('jenis_acara')
                            ->options([
                                'Wedding' => 'Wedding',
                                'Corporate' => 'Corporate',
                                'Birthday' => 'Birthday',
                                'Other' => 'Other',
                            ])
                            ->label('Jenis'),
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->required()
                                    ->label('Tanggal'),
                                TimePicker::make('jam')
                                    ->label('Jam'),
                            ]),
                        TextInput::make('venue')
                            ->label('Venue'),
                        Textarea::make('catatan_khusus')
                            ->label('Catatan Khusus'),

                        // The Cart UI that connects with the left menu
                        ViewField::make('cart_ui')
                            ->view('filament.forms.components.pos-cart')
                            ->columnSpanFull(),

                        // Hidden fields for total calculation & state
                        Hidden::make('cart_data')->default('[]')->extraAttributes(['class' => 'cart-data-input']),
                        Hidden::make('subtotal')->default(0)->extraAttributes(['class' => 'subtotal-input']),
                        Hidden::make('total')->default(0)->extraAttributes(['class' => 'total-input']),
                        
                        TextInput::make('diskon')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->label('Diskon')
                            ->extraInputAttributes(['x-on:input.debounce.500ms' => '$dispatch(\'update-diskon\', $el.value)']),
                        TextInput::make('biaya_tambahan')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->label('Biaya Tambahan')
                            ->extraInputAttributes(['x-on:input.debounce.500ms' => '$dispatch(\'update-biaya\', $el.value)']),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Informasi Utama')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('no_order')->label('No. Order'),
                        \Filament\Infolists\Components\TextEntry::make('customer.nama')->label('Pelanggan'),
                        \Filament\Infolists\Components\TextEntry::make('nama_acara')->label('Acara'),
                        \Filament\Infolists\Components\TextEntry::make('jenis_acara')->label('Jenis Acara'),
                        \Filament\Infolists\Components\TextEntry::make('tanggal')->label('Tanggal')->date('d M Y'),
                        \Filament\Infolists\Components\TextEntry::make('jam')->label('Jam'),
                        \Filament\Infolists\Components\TextEntry::make('venue')->label('Venue'),
                        \Filament\Infolists\Components\TextEntry::make('status')->label('Status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pesanan masuk' => 'gray',
                                'dp dibayar' => 'warning',
                                'selesai' => 'success',
                                default => 'primary',
                            }),
                    ])->columns(2),
                
                Section::make('Detail Keuangan')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('subtotal')->label('Subtotal')->money('idr'),
                        \Filament\Infolists\Components\TextEntry::make('diskon')->label('Diskon')->money('idr'),
                        \Filament\Infolists\Components\TextEntry::make('biaya_tambahan')->label('Biaya Tambahan')->money('idr'),
                        \Filament\Infolists\Components\TextEntry::make('total')->label('Total')->money('idr')->weight('bold'),
                    ])->columns(2),

                Section::make('Item Pesanan')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('product.nama')->label('Produk'),
                                \Filament\Infolists\Components\TextEntry::make('qty')->label('Qty'),
                                \Filament\Infolists\Components\TextEntry::make('harga')->label('Harga')->money('idr'),
                                \Filament\Infolists\Components\TextEntry::make('subtotal')->label('Subtotal')->money('idr'),
                            ])
                            ->columns(4)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_order')
                    ->label('NO. ORDER')
                    ->searchable()
                    ->sortable()
                    ->color('warning')
                    ->weight('bold'),
                TextColumn::make('nama_acara')
                    ->label('ACARA')
                    ->searchable(),
                TextColumn::make('customer.nama')
                    ->label('PELANGGAN')
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('TANGGAL')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('TOTAL')
                    ->money('idr')
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pesanan masuk' => 'gray',
                        'dp dibayar' => 'warning',
                        'selesai' => 'success',
                        default => 'primary',
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('print_invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (\App\Models\Order $record) => route('invoice.download', $record))
                    ->openUrlInNewTab(),
                \Filament\Actions\ViewAction::make()->label('Detail'),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
