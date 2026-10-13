<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class KaryawanResource extends Resource
{
    protected static ?string $model = User::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SDM & PENGGAJIAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Karyawan';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getPluralModelLabel(): string
    {
        return 'Karyawan';
    }

    public static function getModelLabel(): string
    {
        return 'Karyawan';
    }

    public static function getEloquentQuery(): Builder
    {
        // Prioritaskan atau sertakan employeeProfile
        return parent::getEloquentQuery()->with('employeeProfile');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun & Akses')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Kosongkan saat edit jika tidak ingin mengubah password'),
                        Select::make('role')
                            ->label('Hak Akses Sistem')
                            ->options([
                                'admin' => 'Admin',
                                'crew'  => 'Crew / Lapangan',
                            ])
                            ->default('crew')
                            ->required(),
                    ]),

                Section::make('Profil Kepegawaian')
                    ->relationship('employeeProfile')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK / No. Induk')
                            ->unique(table: 'employee_profiles', column: 'nik', ignorable: fn ($record) => $record),
                        TextInput::make('jabatan')
                            ->label('Jabatan / Posisi')
                            ->placeholder('Contoh: Kepala Dekor, Crew Catering, Driver'),
                        Select::make('tipe')
                            ->label('Tipe Karyawan')
                            ->options([
                                'bulanan' => 'Bulanan (Gaji Tetap)',
                                'harian'  => 'Harian / Per Event',
                            ])
                            ->default('harian')
                            ->required()
                            ->live(),
                        TextInput::make('gaji_pokok')
                            ->label(fn ($get) => $get('tipe') === 'harian' ? 'Upah Standar / Hari' : 'Gaji Pokok Bulanan')
                            ->currencyIdr()
                            ->required()
                            ->default(0),
                        TextInput::make('upah_per_event')
                            ->label('Upah Flat per Event (Opsional)')
                            ->currencyIdr()
                            ->helperText('Jika diisi, perhitungan payroll harian mengacu pada jumlah SPK yang dihadiri dikali nominal ini')
                            ->visible(fn ($get) => $get('tipe') === 'harian'),
                        TextInput::make('tunjangan_transport')
                            ->label('Tunjangan Transport')
                            ->currencyIdr()
                            ->default(0),
                        TextInput::make('tunjangan_makan')
                            ->label('Tunjangan Makan')
                            ->currencyIdr()
                            ->default(0),
                        TextInput::make('no_hp')
                            ->label('No. Telepon / WhatsApp')
                            ->tel(),
                        DatePicker::make('tanggal_bergabung')
                            ->label('Tanggal Bergabung')
                            ->default(now()),
                        Toggle::make('status_aktif')
                            ->label('Status Aktif Bekerja')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('employeeProfile.nik')
                    ->label('NIK')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('employeeProfile.jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->placeholder('Crew'),
                TextColumn::make('employeeProfile.tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'bulanan' => 'success',
                        'harian'  => 'warning',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? '-')),
                TextColumn::make('employeeProfile.gaji_pokok')
                    ->label('Gaji / Upah')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('employeeProfile.no_hp')
                    ->label('Kontak')
                    ->placeholder('-'),
                IconColumn::make('employeeProfile.status_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe Karyawan')
                    ->relationship('employeeProfile', 'tipe')
                    ->options([
                        'bulanan' => 'Bulanan',
                        'harian'  => 'Harian',
                    ]),
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
            'index'  => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit'   => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
