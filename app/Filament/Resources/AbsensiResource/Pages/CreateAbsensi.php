<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dicatat_oleh'] = auth()->id();
        $data['denda'] = blank($data['denda'] ?? null) ? 0 : (float) $data['denda'];
        $data['jam_masuk'] = blank($data['jam_masuk'] ?? null) ? null : $data['jam_masuk'];
        $data['jam_pulang'] = blank($data['jam_pulang'] ?? null) ? null : $data['jam_pulang'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
