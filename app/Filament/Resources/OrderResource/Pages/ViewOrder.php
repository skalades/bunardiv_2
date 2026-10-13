<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->modalHeading(fn (\App\Models\Order $record) => "Hapus Pesanan {$record->no_order}")
                ->modalDescription('Apakah Anda yakin ingin menghapus pesanan ini? Semua rincian item, invoice, dan riwayat pembayaran terkait akan dihapus secara permanen.'),
        ];
    }
}
