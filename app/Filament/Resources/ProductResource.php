<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-squares-plus';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'OPERASIONAL';
    }

    public static function getNavigationLabel(): string
    {
        return 'Menu & Paket POS';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Menu & Paket Layanan';
    }

    public static function getModelLabel(): string
    {
        return 'Produk / Paket Layanan';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Menu / Paket Layanan')
                    ->description('Menu atau paket yang ditambahkan di sini akan langsung tampil pada layar kasir POS pesanan acara.')
                    ->columns(2)
                    ->schema([
                        Select::make('kategori')
                            ->label('Kategori Layanan')
                            ->options([
                                'CATERING'   => 'Catering (Makanan, Prasmanan, Snack)',
                                'DECORATION' => 'Decoration (Pelaminan, Tenda, Sound, Properti)',
                            ])
                            ->default('CATERING')
                            ->required(),

                        TextInput::make('nama')
                            ->label('Nama Menu / Paket')
                            ->placeholder('Contoh: Paket Prasmanan Deluxe atau Backdrop Rustic')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('harga')
                            ->label('Harga Satuan / Paket')
                            ->currencyIdr()
                            ->required()
                            ->helperText('Harga per porsi atau harga paket per event.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Menu / Paket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'CATERING'   => 'warning',
                        'DECORATION' => 'info',
                        default      => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga (Rp)')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'CATERING'   => 'Catering',
                        'DECORATION' => 'Decoration',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
