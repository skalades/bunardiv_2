<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_payroll')
                ->label('Hitung / Generate Payroll')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->form([
                    Select::make('tipe_periode')
                        ->label('Tipe Payroll')
                        ->options([
                            'harian' => 'Harian (Per Hari / Event)',
                            'bulanan' => 'Bulanan (Rekap Sebulan Penuh)',
                        ])
                        ->default('harian')
                        ->required()
                        ->live(),
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default(now())
                        ->required()
                        ->visible(fn ($get) => $get('tipe_periode') === 'harian'),
                    Toggle::make('only_attended')
                        ->label('Hanya Karyawan yang Bertugas / Hadir')
                        ->helperText('Hanya masukkan kru yang hadir atau bertugas pada tanggal ini ke daftar slip gaji')
                        ->default(true)
                        ->visible(fn ($get) => $get('tipe_periode') === 'harian'),
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required()
                        ->visible(fn ($get) => $get('tipe_periode') === 'bulanan'),
                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(array_combine(range(2024, 2030), range(2024, 2030)))
                        ->default(now()->year)
                        ->required()
                        ->visible(fn ($get) => $get('tipe_periode') === 'bulanan'),
                ])
                ->action(function (array $data) {
                    try {
                        $service = app(PayrollService::class);
                        $userId = auth()->id() ?? 1;

                        if (($data['tipe_periode'] ?? 'harian') === 'harian') {
                            $payroll = $service->generateDaily(
                                $data['tanggal'],
                                $userId,
                                (bool) ($data['only_attended'] ?? true)
                            );
                        } else {
                            $payroll = $service->generate(
                                (int) $data['bulan'],
                                (int) $data['tahun'],
                                $userId
                            );
                        }

                        Notification::make()
                            ->title("Payroll {$payroll->nama_bulan} berhasil dihitung untuk {$payroll->items()->count()} karyawan.")
                            ->success()
                            ->send();

                        $this->redirect(PayrollResource::getUrl('edit', ['record' => $payroll]));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Gagal: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
