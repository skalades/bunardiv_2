<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Attendance;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AbsensiResource extends Resource
{
    protected static ?string $model = Attendance::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user.employeeProfile', 'pencatat']);
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SDM & PENGGAJIAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Absensi';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getPluralModelLabel(): string
    {
        return 'Absensi Karyawan';
    }

    public static function getModelLabel(): string
    {
        return 'Absensi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kehadiran Karyawan')
                    ->description('Pilih karyawan, tanggal, dan status kehadiran.')
                    ->columns(3)
                    ->schema([
                        Select::make('user_id')
                            ->label('Nama Karyawan')
                            ->options(function () {
                                return User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                                    ->with('employeeProfile')
                                    ->get()
                                    ->mapWithKeys(function ($u) {
                                        $jabatan = $u->employeeProfile?->jabatan ?? 'Staf';
                                        $tipe = ucfirst($u->employeeProfile?->tipe ?? 'harian');

                                        return [$u->id => "{$u->name} ({$jabatan} - {$tipe})"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('tanggal')
                            ->label('Tanggal Kehadiran')
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
                    ]),

                Section::make('Jam Kerja & Pengaturan Denda')
                    ->description('Pilih preset shift atau input jam masuk dan pulang secara manual. Denda dapat diatur dan disesuaikan.')
                    ->columns(2)
                    ->schema([
                        Select::make('preset_shift')
                            ->label('Pilihan Shift Cepat')
                            ->options([
                                'custom' => 'Input Manual (Custom)',
                                'kantor' => 'Kantor Normal (08:00 - 17:00)',
                                'shift_1' => 'Shift 1: Pagi (06:00 - 15:00)',
                                'shift_2' => 'Shift 2: Siang (09:00 - 18:00)',
                                'shift_3' => 'Shift 3: Sore (11:00 - 20:00)',
                                'catering' => 'Crew Catering / Kitchen (06:00 - 16:00)',
                                'decor' => 'Crew Decor (07:00 - 18:00)',
                                'bangunan' => 'Tukang Bangunan (07:00 - 17:00)',
                            ])
                            ->default('kantor')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
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
                            })
                            ->columnSpanFull(),

                        TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->default('08:00')
                            ->seconds(false)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $preset = $get('preset_shift') ?? 'kantor';
                                $shiftStarts = [
                                    'kantor' => '08:00',
                                    'shift_1' => '06:00',
                                    'shift_2' => '09:00',
                                    'shift_3' => '11:00',
                                    'catering' => '06:00',
                                    'decor' => '07:00',
                                    'bangunan' => '07:00',
                                ];
                                $scheduledStart = $shiftStarts[$preset] ?? '08:00';
                                $calculatedDenda = Attendance::hitungDendaTelat($state, $scheduledStart, 5000);
                                $set('denda', $calculatedDenda);
                            }),

                        TimePicker::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->default('17:00')
                            ->seconds(false),

                        TextInput::make('denda')
                            ->label('Denda Keterlambatan / Penalti')
                            ->currencyIdr()
                            ->default(0)
                            ->helperText('Denda dapat diatur atau disesuaikan secara manual oleh admin. Rekomendasi otomatis: Rp 5.000 per 30 menit keterlambatan.')
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan / Alasan')
                            ->placeholder('Opsional (alasan izin, dispensasi, catatan lembur/terlambat, dll)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.employeeProfile.jabatan')
                    ->label('Jabatan')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpha' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('jam_kerja')
                    ->label('Jam Kerja (Masuk - Pulang)')
                    ->getStateUsing(fn (Attendance $record) => $record->jam_kerja)
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('denda')
                    ->label('Denda')
                    ->money('idr', decimalPlaces: 0)
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'gray')
                    ->weight(fn ($state) => (float) $state > 0 ? 'bold' : 'normal')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->placeholder('-'),
                TextColumn::make('pencatat.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
                Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query) => $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)),
                Filter::make('ada_denda')
                    ->label('Memiliki Denda')
                    ->query(fn (Builder $query) => $query->where('denda', '>', 0)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('set_hadir')
                        ->label('Tandai Hadir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'hadir', 'dicatat_oleh' => auth()->id()]);
                            Notification::make()->title('Semua data terpilih diubah menjadi Hadir')->success()->send();
                        }),
                    BulkAction::make('set_izin')
                        ->label('Tandai Izin')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'izin', 'dicatat_oleh' => auth()->id()]);
                            Notification::make()->title('Semua data terpilih diubah menjadi Izin')->warning()->send();
                        }),
                    BulkAction::make('set_alpha')
                        ->label('Tandai Alpha')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'alpha', 'dicatat_oleh' => auth()->id()]);
                            Notification::make()->title('Semua data terpilih diubah menjadi Alpha')->danger()->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
