<?php

namespace App\Filament\Resources\PengeluaranResource\Pages;

use App\Filament\Resources\PengeluaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengeluarans extends ListRecords
{
    protected static string $resource = PengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Catat Pengeluaran Baru'),
        ];
    }
}
