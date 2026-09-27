<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ManageInvoices;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'no_invoice';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_invoice')
                    ->required(),
                TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                TextInput::make('judul_tagihan'),
                TextInput::make('nominal_tagihan')
                    ->required()
                    ->numeric(),
                DatePicker::make('jatuh_tempo'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('no_invoice'),
                TextEntry::make('order_id')
                    ->numeric(),
                TextEntry::make('judul_tagihan')
                    ->placeholder('-'),
                TextEntry::make('nominal_tagihan')
                    ->numeric(),
                TextEntry::make('jatuh_tempo')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_invoice')
            ->columns([
                TextColumn::make('no_invoice')
                    ->searchable(),
                TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('judul_tagihan')
                    ->searchable(),
                TextColumn::make('nominal_tagihan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jatuh_tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ManageInvoices::route('/'),
        ];
    }
}
