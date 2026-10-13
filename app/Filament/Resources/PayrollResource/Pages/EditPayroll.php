<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\Payroll;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('finalisasi')
                ->label('Kunci & Finalkan')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->visible(fn (Payroll $record) => $record->status === 'draft')
                ->requiresConfirmation()
                ->modalHeading('Finalisasi Payroll?')
                ->modalDescription('Semua pemotongan kasbon otomatis akan langsung diproses dan payroll akan dikunci permanen.')
                ->action(function (Payroll $record) {
                    try {
                        app(PayrollService::class)->finalize($record);
                        Notification::make()
                            ->title('Payroll berhasil difinalisasi.')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'tanggal_proses']);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Gagal: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make()
                ->visible(fn (Payroll $record) => $record->status === 'draft'),
        ];
    }
}
