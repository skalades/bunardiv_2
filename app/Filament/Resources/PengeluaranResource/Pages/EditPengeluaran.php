<?php

namespace App\Filament\Resources\PengeluaranResource\Pages;

use App\Filament\Resources\PengeluaranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaran extends EditRecord
{
    protected static string $resource = PengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
