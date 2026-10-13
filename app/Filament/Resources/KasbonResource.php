<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KasbonResource\Pages;
use App\Filament\Resources\KasbonResource\RelationManagers\PaymentsRelationManager;
use App\Models\EmployeeLoan;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KasbonResource extends Resource
{
    protected static ?string $model = EmployeeLoan::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SDM & PENGGAJIAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kasbon & Piutang';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kasbon Karyawan';
    }

    public static function getModelLabel(): string
    {
        return 'Kasbon';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengajuan Kasbon')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Karyawan')
                            ->options(
                                User::whereHas('employeeProfile', fn ($q) => $q->where('status_aktif', true))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('tanggal_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->default(now())
                            ->required(),
                        TextInput::make('jumlah')
                            ->label('Total Pinjaman / Kasbon')
                            ->currencyIdr()
                            ->required()
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($set, $state, string $operation) {
                                if ($operation === 'create') {
                                    $set('sisa_hutang', $state);
                                }
                            }),
                        TextInput::make('sisa_hutang')
                            ->label('Sisa Hutang Saat Ini')
                            ->currencyIdr()
                            ->disabled()
                            ->dehydrated(),
                        Select::make('metode_cicilan')
                            ->label('Skema Pembayaran')
                            ->options([
                                'manual'           => 'Manual (Dibayar Langsung oleh Karyawan)',
                                'auto_potong_gaji' => 'Auto Potong Gaji (Payroll Bulanan)',
                            ])
                            ->default('manual')
                            ->required()
                            ->live(),
                        TextInput::make('cicilan_per_bulan')
                            ->label('Besaran Potongan per Bulan')
                            ->currencyIdr()
                            ->visible(fn ($get) => $get('metode_cicilan') === 'auto_potong_gaji')
                            ->required(fn ($get) => $get('metode_cicilan') === 'auto_potong_gaji')
                            ->helperText('Nominal yang akan dipotong otomatis setiap kali payroll digenerate'),
                        Select::make('status')
                            ->label('Status Persetujuan')
                            ->options([
                                'pending'   => 'Menunggu Persetujuan',
                                'disetujui' => 'Disetujui',
                                'lunas'     => 'Lunas',
                                'ditolak'   => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required(),
                        Textarea::make('catatan')
                            ->label('Keperluan / Catatan')
                            ->placeholder('Contoh: Keperluan berobat keluarga')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('tanggal_pengajuan')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('jumlah')
                    ->label('Total Kasbon')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('sisa_hutang')
                    ->label('Sisa Hutang')
                    ->money('idr', decimalPlaces: 0)
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'gray',
                        'disetujui' => 'warning',
                        'lunas'     => 'success',
                        'ditolak'   => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('metode_cicilan')
                    ->label('Skema')
                    ->badge()
                    ->color(fn ($state) => $state === 'auto_potong_gaji' ? 'info' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === 'auto_potong_gaji' ? 'Auto Potong Gaji' : 'Manual'),
                TextColumn::make('cicilan_per_bulan')
                    ->label('Cicilan/Bulan')
                    ->money('idr', decimalPlaces: 0)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'disetujui' => 'Disetujui',
                        'lunas'     => 'Lunas',
                        'ditolak'   => 'Ditolak',
                    ]),
                SelectFilter::make('metode_cicilan')
                    ->options([
                        'manual'           => 'Manual',
                        'auto_potong_gaji' => 'Auto Potong Gaji',
                    ]),
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (EmployeeLoan $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (EmployeeLoan $record) {
                        $record->update([
                            'status'            => 'disetujui',
                            'tanggal_disetujui' => now()->toDateString(),
                            'disetujui_oleh'    => auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Kasbon telah disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('bayar_sebagian')
                    ->label('Bayar Cicilan')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn (EmployeeLoan $record) => $record->status === 'disetujui' && $record->sisa_hutang > 0)
                    ->form([
                        TextInput::make('jumlah_bayar')
                            ->label('Nominal Pembayaran')
                            ->currencyIdr()
                            ->required()
                            ->default(fn (EmployeeLoan $record) => (int) round($record->sisa_hutang))
                            ->helperText(fn (EmployeeLoan $record) => 'Sisa saat ini: Rp ' . number_format($record->sisa_hutang, 0, ',', '.')),
                        DatePicker::make('tanggal')
                            ->label('Tanggal Pembayaran')
                            ->default(now())
                            ->required(),
                        Textarea::make('catatan')
                            ->label('Catatan Bukti'),
                    ])
                    ->action(function (EmployeeLoan $record, array $data) {
                        $bayar = min((float) $data['jumlah_bayar'], (float) $record->sisa_hutang);
                        $record->payments()->create([
                            'jumlah_bayar' => $bayar,
                            'tanggal'      => $data['tanggal'],
                            'sumber'       => 'manual',
                            'dicatat_oleh' => auth()->id(),
                            'catatan'      => $data['catatan'] ?? null,
                        ]);
                        Notification::make()
                            ->title('Pembayaran cicilan Rp ' . number_format($bayar, 0, ',', '.') . ' berhasil dicatat')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (EmployeeLoan $record) => $record->status === 'pending' || $record->payments()->count() === 0),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKasbons::route('/'),
            'create' => Pages\CreateKasbon::route('/create'),
            'edit'   => Pages\EditKasbon::route('/{record}/edit'),
        ];
    }
}
