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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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
                ->modalDescription('Pilih tanggal, shift / jam kerja, dan centang karyawan yang ingin dicatat absensinya secara sekaligus.')
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
                        ->required()
                        ->live(),
                    Select::make('preset_shift')
                        ->label('Pilihan Shift Jam Kerja')
                        ->options([
                            'custom' => 'Input Jam Manual (Custom)',
                            'kantor' => 'Kantor Normal (08:00 - 17:00)',
                            'shift_1' => 'Shift 1: Pagi (06:00 - 15:00)',
                            'shift_2' => 'Shift 2: Siang (09:00 - 18:00)',
                            'shift_3' => 'Shift 3: Sore (11:00 - 20:00)',
                            'catering' => 'Crew Catering / Kitchen (06:00 - 16:00)',
                            'decor' => 'Crew Decor (07:00 - 18:00)',
                            'bangunan' => 'Tukang Bangunan (07:00 - 17:00)',
                        ])
                        ->default('kantor')
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            $shifts = [
                                'kantor' => ['masuk' => '08:00', 'pulang' => '17:00'],
                                'shift_1' => ['masuk' => '06:00', 'pulang' => '15:00'],
                                'shift_2' => ['masuk' => '09:00', 'pulang' => '18:00'],
                                'shift_3' => ['masuk' => '11:00', 'pulang' => '20:00'],
                                'catering' => ['masuk' => '06:00', 'pulang' => '16:00'],
                                'decor' => ['masuk' => '07:00', 'pulang' => '18:00'],
                                'bangunan' => ['masuk' => '07:00', 'pulang' => '17:00'],
                            ];

                            if (isset($shifts[$state])) {
                                $set('jam_masuk', $shifts[$state]['masuk']);
                                $set('jam_pulang', $shifts[$state]['pulang']);
                                $set('denda', 0);
                            }
                        }),
                    TimePicker::make('jam_masuk')
                        ->label('Jam Masuk')
                        ->default('08:00')
                        ->seconds(false),
                    TimePicker::make('jam_pulang')
                        ->label('Jam Pulang')
                        ->default('17:00')
                        ->seconds(false),
                    TextInput::make('denda')
                        ->label('Denda Keterlambatan / Penyesuaian (Bisa diatur)')
                        ->currencyIdr()
                        ->default(0)
                        ->helperText('Nominal denda per karyawan (Standar: Rp 5.000 / 30 menit telat). Default: Rp 0.'),
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
                    $jamMasuk = $data['jam_masuk'] ?? null;
                    $jamPulang = $data['jam_pulang'] ?? null;
                    $denda = isset($data['denda']) ? (float) $data['denda'] : 0;
                    $count = 0;

                    foreach ($data['user_ids'] as $userId) {
                        Attendance::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'tanggal' => $tanggal,
                            ],
                            [
                                'status' => $status,
                                'jam_masuk' => $jamMasuk,
                                'jam_pulang' => $jamPulang,
                                'denda' => $denda,
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
