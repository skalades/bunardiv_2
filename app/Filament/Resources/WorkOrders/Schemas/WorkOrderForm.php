<?php

namespace App\Filament\Resources\WorkOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WorkOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'nama_acara')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Pesanan (Order)'),
                Select::make('pic_id')
                    ->relationship('pic', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('PIC (Penanggung Jawab)'),
                TextInput::make('spk_number')
                    ->label('Nomor SPK')
                    ->placeholder('Otomatis Dibuat')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('surat_jalan_number')
                    ->label('Nomor Surat Jalan')
                    ->placeholder('Otomatis Dibuat')
                    ->disabled()
                    ->dehydrated(),
                DatePicker::make('tanggal_persiapan')
                    ->required()
                    ->label('Tanggal Persiapan'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Sedang Dipersiapkan',
                        'ready' => 'Siap Dikirim',
                        'completed' => 'Selesai'
                    ])
                    ->required()
                    ->default('pending'),
                Textarea::make('catatan')
                    ->columnSpanFull()
                    ->label('Catatan Persiapan'),
            ]);
    }
}
