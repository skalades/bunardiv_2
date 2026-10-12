<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Attendance;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensiResource extends Resource
{
    protected static ?string $model = Attendance::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
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
                Section::make('Pencatatan Kehadiran')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Karyawan (Khusus Bulanan)')
                            ->options(
                                User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                                    ->pluck('name', 'id')
                            )
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
                                'izin'  => 'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpha',
                            ])
                            ->default('hadir')
                            ->required(),
                        Textarea::make('keterangan')
                            ->label('Keterangan / Alasan')
                            ->placeholder('Opsional (alasan izin, surat dokter, dll)')
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
                        'izin'  => 'warning',
                        'sakit' => 'info',
                        'alpha' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
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
                        'izin'  => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
                Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query) => $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit'   => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
