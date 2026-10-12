<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranResource\Pages;
use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-trending-down';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'KEUANGAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengeluaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengeluaran';
    }

    public static function getModelLabel(): string
    {
        return 'Pengeluaran';
    }

    public static array $kategoriOptions = [
        'Bahan Baku & Catering' => 'Bahan Baku & Catering (Sayur, Daging, Bumbu, dll)',
        'Properti & Dekorasi'   => 'Properti & Dekorasi (Bunga Segar, Kain, Lampu, dll)',
        'Transport & Logistik'  => 'Transport & Logistik (BBM, Tol, Sewa Truk/Pickup)',
        'Konsumsi Tim'          => 'Konsumsi Tim (Makan & Minum Kru Lapangan)',
        'Operasional & Kantor'  => 'Operasional & Kantor (Listrik, Air, ATK, Kebersihan)',
        'Pemeliharaan Alat'     => 'Pemeliharaan Alat (Service, Cuci Alat, Perbaikan)',
        'Pemasaran & Iklan'     => 'Pemasaran & Iklan (Brosur, Medsos, Ads)',
        'Lain-lain'             => 'Lain-lain',
    ];

    public static array $metodeOptions = [
        'Tunai / Cash'     => 'Tunai / Cash',
        'Transfer BCA'     => 'Transfer BCA',
        'Transfer Mandiri' => 'Transfer Mandiri',
        'Transfer BRI'     => 'Transfer BRI',
        'QRIS / E-Wallet'  => 'QRIS / E-Wallet',
        'Kartu Kredit'     => 'Kartu Kredit',
        'Lainnya'          => 'Lainnya',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Pengeluaran')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nomor_transaksi')
                            ->label('No. Transaksi')
                            ->placeholder('Auto-generated (EXP-YYYYMMDD-XXXX)')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        DatePicker::make('tanggal')
                            ->label('Tanggal Pengeluaran')
                            ->required()
                            ->default(now()),
                        Select::make('kategori')
                            ->label('Kategori Pengeluaran')
                            ->options(static::$kategoriOptions)
                            ->required()
                            ->searchable(),
                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options(static::$metodeOptions)
                            ->default('Tunai / Cash')
                            ->required(),
                        TextInput::make('nominal')
                            ->label('Nominal Pengeluaran')
                            ->currencyIdr()
                            ->required(),
                        TextInput::make('judul')
                            ->label('Keterangan / Keperluan')
                            ->placeholder('Contoh: Beli Bunga Mawar 50 Ikat di Pasar Bunga')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Alokasi Proyek & Penanggung Jawab')
                    ->columns(2)
                    ->schema([
                        Select::make('order_id')
                            ->label('Terkait Pesanan / Event (Opsional)')
                            ->options(function () {
                                return Order::latest('tanggal')
                                    ->take(100)
                                    ->get()
                                    ->mapWithKeys(fn ($o) => [$o->id => "{$o->no_order} - {$o->nama_acara}"]);
                            })
                            ->searchable()
                            ->placeholder('Pilih jika pengeluaran untuk event tertentu')
                            ->helperText('Kosongkan bila merupakan operasional umum kantor/gudang.'),
                        Select::make('pic_id')
                            ->label('Penanggung Jawab / PIC')
                            ->options(User::pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required()
                            ->searchable(),
                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->placeholder('Rincian catatan pengeluaran')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('nomor_transaksi')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Bahan Baku & Catering' => 'warning',
                        'Properti & Dekorasi'   => 'info',
                        'Transport & Logistik'  => 'primary',
                        'Konsumsi Tim'          => 'gray',
                        'Operasional & Kantor'  => 'success',
                        'Pemeliharaan Alat'     => 'danger',
                        default                 => 'gray',
                    }),
                TextColumn::make('judul')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->weight('bold')
                    ->color('danger'),
                TextColumn::make('order.no_order')
                    ->label('Alokasi Event')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Operasional Umum'),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('pic.name')
                    ->label('PIC')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->options(static::$kategoriOptions),
                SelectFilter::make('metode_pembayaran')
                    ->options(static::$metodeOptions),
                SelectFilter::make('order_id')
                    ->label('Event / Pesanan')
                    ->relationship('order', 'nama_acara')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPengeluarans::route('/'),
            'create' => Pages\CreatePengeluaran::route('/create'),
            'edit'   => Pages\EditPengeluaran::route('/{record}/edit'),
        ];
    }
}
