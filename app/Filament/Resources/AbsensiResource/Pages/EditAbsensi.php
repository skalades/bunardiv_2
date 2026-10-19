<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAbsensi extends EditRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
