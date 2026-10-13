<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_attendance')
                ->label('Presensi Massal (Bulk)')
                ->icon('heroicon-o-user-group')
                ->color('warning')
                ->modalHeading('Presensi Karyawan Secara Massal')
                ->modalDescription('Pilih tanggal dan centang karyawan yang ingin dicatat absensinya secara sekaligus.')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Tanggal Presensi')
                        ->default(now())
                        ->required(),
                    Select::make('status')
                        ->label('Status Kehadiran')
                        ->options([
                            'hadir' => 'Hadir',
                            'izin' => 'Izin',
                            'sakit' => 'Sakit',
                            'alpha' => 'Alpha',
                        ])
                        ->default('hadir')
                        ->required(),
                    CheckboxList::make('user_ids')
                        ->label('Pilih Karyawan')
                        ->options(function () {
                            return User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                                ->with('employeeProfile')
                                ->get()
                                ->mapWithKeys(function ($u) {
                                    $jabatan = $u->employeeProfile?->jabatan ?? 'Crew';
                                    $tipe = ucfirst($u->employeeProfile?->tipe ?? 'harian');

                                    return [$u->id => "{$u->name} ({$jabatan} - {$tipe})"];
                                });
                        })
                        ->default(function () {
                            return User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                                ->pluck('id')
                                ->toArray();
                        })
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable()
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan / Catatan (Opsional)')
                        ->placeholder('Contoh: Presensi massal kantor'),
                ])
                ->action(function (array $data) {
                    $tanggal = Carbon::parse($data['tanggal'])->toDateString();
                    $status = $data['status'];
                    $keterangan = $data['keterangan'] ?? 'Presensi massal manual oleh Super Admin';
                    $adminId = auth()->id();
                    $count = 0;

                    foreach ($data['user_ids'] as $userId) {
                        Attendance::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'tanggal' => $tanggal,
                            ],
                            [
                                'status' => $status,
                                'keterangan' => $keterangan,
                                'dicatat_oleh' => $adminId,
                            ]
                        );
                        $count++;
                    }

                    Notification::make()
                        ->title("Presensi massal berhasil disimpan untuk {$count} karyawan.")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Catat Absensi Tunggal'),
        ];
    }
}
