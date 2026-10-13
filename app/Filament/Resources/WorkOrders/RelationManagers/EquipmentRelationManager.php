<?php

namespace App\Filament\Resources\WorkOrders\RelationManagers;

use App\Models\WorkOrderEquipment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    protected static ?string $title = 'Peralatan';

    protected static ?string $modelLabel = 'Peralatan';

    protected static ?string $pluralModelLabel = 'Peralatan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('inventory_id')
                    ->relationship('inventory', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Nama Barang/Peralatan'),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('Jumlah'),
                Forms\Components\Select::make('status')
                    ->options([
                        'disiapkan' => 'Disiapkan',
                        'dipinjam' => 'Dipinjam / Digunakan',
                        'dikembalikan' => 'Dikembalikan',
                    ])
                    ->required()
                    ->default('disiapkan')
                    ->label('Status Barang'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inventory.nama')
            ->columns([
                Tables\Columns\TextColumn::make('inventory.nama')
                    ->label('Nama Barang')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('inventory.kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('inventory.satuan')
                    ->label('Satuan')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disiapkan' => 'warning',
                        'dipinjam' => 'info',
                        'dikembalikan' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'disiapkan' => 'Disiapkan',
                        'dipinjam' => 'Dipinjam / Digunakan',
                        'dikembalikan' => 'Dikembalikan',
                    ]),
            ])
            ->headerActions([
                // AKSI UTAMA: Mode POS / Katalog Barang
                Action::make('pos_equipment')
                    ->label('+ Pilih Peralatan (Mode POS)')
                    ->icon('heroicon-o-squares-plus')
                    ->color('warning')
                    ->modalHeading('Katalog Pemilihan Peralatan SPK (Mode POS)')
                    ->modalWidth('7xl')
                    ->modalSubmitActionLabel('Simpan Peralatan ke SPK')
                    ->form([
                        Forms\Components\ViewField::make('pos_equipment_picker')
                            ->view('filament.forms.components.pos-equipment-picker')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('selected_equipment_data'),
                        Forms\Components\Select::make('default_status')
                            ->label('Status untuk Semua Peralatan yang Dipilih')
                            ->options([
                                'disiapkan' => 'Disiapkan',
                                'dipinjam' => 'Dipinjam / Digunakan',
                                'dikembalikan' => 'Dikembalikan',
                            ])
                            ->default('disiapkan')
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $items = json_decode($data['selected_equipment_data'] ?? '[]', true);
                        if (empty($items)) {
                            Notification::make()
                                ->title('Tidak ada peralatan yang dipilih')
                                ->warning()
                                ->send();

                            return;
                        }

                        $workOrder = $livewire->getOwnerRecord();
                        $status = $data['default_status'] ?? 'disiapkan';
                        $countAdded = 0;

                        foreach ($items as $item) {
                            $invId = $item['id'];
                            $qty = max(1, (int) ($item['qty'] ?? 1));

                            $existing = WorkOrderEquipment::where('work_order_id', $workOrder->id)
                                ->where('inventory_id', $invId)
                                ->first();

                            if ($existing) {
                                $existing->update([
                                    'jumlah' => $existing->jumlah + $qty,
                                    'status' => $status,
                                ]);
                            } else {
                                WorkOrderEquipment::create([
                                    'work_order_id' => $workOrder->id,
                                    'inventory_id' => $invId,
                                    'jumlah' => $qty,
                                    'status' => $status,
                                ]);
                            }
                            $countAdded++;
                        }

                        Notification::make()
                            ->title("Berhasil menambahkan {$countAdded} peralatan ke SPK!")
                            ->success()
                            ->send();
                    }),

                // Cadangan: Input Manual per 1 barang jika diperlukan
                CreateAction::make()
                    ->label('Input Manual (1 Barang)')
                    ->icon('heroicon-o-plus')
                    ->color('gray'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
