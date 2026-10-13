<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager;
use App\Models\Customer;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['customer']);
    }

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

    public static function getNavigationSort(): ?int
    {
        return 2;
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
                            ->columnSpanFull(),
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
                                $customers = Customer::pluck('nama', 'id')->toArray();

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
                                    ->label('Tanggal')
                                    ->default(fn () => request()->query('tanggal', now()->toDateString())),
                                TimePicker::make('jam')
                                    ->label('Jam'),
                            ]),
                        TextInput::make('venue')
                            ->label('Venue'),
                        Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'pesanan masuk' => 'Pesanan Masuk',
                                'dp dibayar' => 'DP Dibayar',
                                'proses' => 'Dalam Proses',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('pesanan masuk')
                            ->required(),
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
                            ->label('Diskon')
                            ->currencyIdr()
                            ->default(0)
                            ->extraInputAttributes(['x-on:input.debounce.500ms' => '$dispatch(\'update-diskon\', $el.value)']),
                        TextInput::make('biaya_tambahan')
                            ->label('Biaya Tambahan')
                            ->currencyIdr()
                            ->default(0)
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
                        TextEntry::make('no_order')->label('No. Order'),
                        TextEntry::make('customer.nama')->label('Pelanggan'),
                        TextEntry::make('nama_acara')->label('Acara'),
                        TextEntry::make('jenis_acara')->label('Jenis Acara'),
                        TextEntry::make('tanggal')->label('Tanggal')->date('d M Y'),
                        TextEntry::make('jam')->label('Jam'),
                        TextEntry::make('venue')->label('Venue'),
                        TextEntry::make('status')->label('Status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pesanan masuk' => 'gray',
                                'dp dibayar' => 'warning',
                                'proses' => 'info',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                default => 'primary',
                            }),
                    ])->columns(2),

                Section::make('Detail Keuangan')
                    ->schema([
                        TextEntry::make('subtotal')->label('Subtotal')->money('idr', decimalPlaces: 0),
                        TextEntry::make('diskon')->label('Diskon')->money('idr', decimalPlaces: 0),
                        TextEntry::make('biaya_tambahan')->label('Biaya Tambahan')->money('idr', decimalPlaces: 0),
                        TextEntry::make('total')->label('Total')->money('idr', decimalPlaces: 0)->weight('bold'),
                    ])->columns(2),

                Section::make('Item Pesanan')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.nama')->label('Produk'),
                                TextEntry::make('qty')->label('Qty'),
                                TextEntry::make('harga')->label('Harga')->money('idr', decimalPlaces: 0),
                                TextEntry::make('subtotal')->label('Subtotal')->money('idr', decimalPlaces: 0),
                            ])
                            ->columns(4),
                    ]),
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
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pesanan masuk' => 'gray',
                        'dp dibayar' => 'warning',
                        'proses' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'primary',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesanan masuk' => 'Pesanan Masuk',
                        'dp dibayar' => 'DP Dibayar',
                        'proses' => 'Dalam Proses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Action::make('ubah_status')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->modalHeading(fn (Order $record) => "Ubah Status: {$record->no_order}")
                    ->modalWidth('md')
                    ->form([
                        Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'pesanan masuk' => 'Pesanan Masuk',
                                'dp dibayar' => 'DP Dibayar',
                                'proses' => 'Dalam Proses',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (Order $record): array => [
                        'status' => $record->status,
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        Notification::make()
                            ->title('Status pesanan berhasil diperbarui')
                            ->body("Status pesanan {$record->no_order} diubah menjadi: ".ucfirst($data['status']))
                            ->success()
                            ->send();
                    }),
                Action::make('print_invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Order $record) => route('invoice.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make()->label('Detail'),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading(fn (Order $record) => "Hapus Pesanan {$record->no_order}")
                    ->modalDescription('Apakah Anda yakin ingin menghapus pesanan ini? Semua rincian item, invoice, dan riwayat pembayaran terkait akan dihapus secara permanen.'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
