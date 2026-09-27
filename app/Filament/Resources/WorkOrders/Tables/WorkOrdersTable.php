<?php

namespace App\Filament\Resources\WorkOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class WorkOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.nama_acara')
                    ->label('Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pic.name')
                    ->label('PIC')
                    ->searchable(),
                TextColumn::make('spk_number')
                    ->label('No. SPK')
                    ->searchable(),
                TextColumn::make('surat_jalan_number')
                    ->label('No. Surat Jalan')
                    ->searchable(),
                TextColumn::make('tanggal_persiapan')
                    ->label('Tgl Persiapan')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'ready' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('cetak_spk')
                    ->label('Cetak SPK')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn (\App\Models\WorkOrder $record) => route('spk.download', $record))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('cetak_sj')
                    ->label('Cetak Surat Jalan')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->url(fn (\App\Models\WorkOrder $record) => route('surat_jalan.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
