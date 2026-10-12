<?php

namespace App\Filament\Resources\KasbonResource\Pages;

use App\Filament\Resources\KasbonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKasbon extends CreateRecord
{
    protected static string $resource = KasbonResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sisa_hutang'] = $data['jumlah'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
