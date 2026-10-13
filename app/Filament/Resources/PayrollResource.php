<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers\ItemsRelationManager;
use App\Models\Payroll;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withSum('items', 'total_bersih');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SDM & PENGGAJIAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penggajian (Payroll)';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getPluralModelLabel(): string
    {
        return 'Payroll';
    }

    public static function getModelLabel(): string
    {
        return 'Payroll';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Periode Penggajian')
                    ->columns(2)
                    ->schema([
                        Select::make('tipe_periode')
                            ->label('Tipe Payroll')
                            ->options([
                                'harian' => 'Harian',
                                'bulanan' => 'Bulanan',
                            ])
                            ->disabled(),
                        DatePicker::make('tanggal')
                            ->label('Tanggal Payroll')
                            ->disabled()
                            ->visible(fn ($get) => $get('tipe_periode') === 'harian'),
                        Select::make('bulan')
                            ->label('Bulan')
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
                            ->disabled()
                            ->visible(fn ($get) => $get('tipe_periode') === 'bulanan'),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(2024, 2030), range(2024, 2030)))
                            ->disabled()
                            ->visible(fn ($get) => $get('tipe_periode') === 'bulanan'),
                        Select::make('status')
                            ->label('Status Payroll')
                            ->options([
                                'draft' => 'Draft (Dapat diedit)',
                                'final' => 'Final (Terkunci & Kasbon Diproses)',
                            ])
                            ->disabled()
                            ->required(),
                        DatePicker::make('tanggal_proses')
                            ->label('Tanggal Finalisasi')
                            ->disabled(),
                        Textarea::make('catatan')
                            ->label('Catatan Periode')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('nama_bulan')
                    ->label('Periode')
                    ->weight('bold'),
                TextColumn::make('tipe_periode')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state === 'harian' ? 'info' : 'primary')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('items_count')
                    ->label('Jumlah Karyawan')
                    ->counts('items')
                    ->badge()
                    ->color('info'),
                TextColumn::make('total_bersih')
                    ->label('Total Penggajian')
                    ->getStateUsing(fn (Payroll $record) => $record->items_sum_total_bersih ?? $record->items->sum('total_bersih'))
                    ->money('idr', decimalPlaces: 0)
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state === 'final' ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                TextColumn::make('tanggal_proses')
                    ->label('Tgl Finalisasi')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipe_periode')
                    ->label('Tipe Periode')
                    ->options([
                        'harian' => 'Harian',
                        'bulanan' => 'Bulanan',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'final' => 'Final',
                    ]),
            ])
            ->recordActions([
                Action::make('finalisasi')
                    ->label('Kunci & Finalkan')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn (Payroll $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Finalisasi Payroll Periode Ini?')
                    ->modalDescription('PERHATIAN: Payroll yang sudah difinalisasi akan otomatis memotong kasbon karyawan bertipe auto-potong dan data tidak dapat diubah lagi.')
                    ->action(function (Payroll $record) {
                        try {
                            app(PayrollService::class)->finalize($record);
                            Notification::make()
                                ->title('Payroll berhasil difinalisasi dan cicilan kasbon telah dipotong.')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal finalisasi: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()->label('Buka Detail'),
                DeleteAction::make()->visible(fn (Payroll $record) => $record->status === 'draft'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
