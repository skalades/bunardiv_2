<?php

namespace App\Filament\Resources\InventoryTransactions;

use App\Filament\Resources\InventoryTransactions\Pages\CreateInventoryTransaction;
use App\Filament\Resources\InventoryTransactions\Pages\EditInventoryTransaction;
use App\Filament\Resources\InventoryTransactions\Pages\ListInventoryTransactions;
use App\Filament\Resources\InventoryTransactions\Schemas\InventoryTransactionForm;
use App\Filament\Resources\InventoryTransactions\Tables\InventoryTransactionsTable;
use App\Models\InventoryTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryTransactionResource extends Resource
{
    protected static ?string $model = InventoryTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return InventoryTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryTransactions::route('/'),
            'create' => CreateInventoryTransaction::route('/create'),
            'edit' => EditInventoryTransaction::route('/{record}/edit'),
        ];
    }
}
