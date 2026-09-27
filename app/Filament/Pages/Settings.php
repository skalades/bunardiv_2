<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'PENGATURAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Sistem';
    }
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Informasi Perusahaan')
                    ->schema([
                        TextInput::make('company_name')->label('Nama Perusahaan')->required(),
                        Textarea::make('company_address')->label('Alamat Lengkap')->columnSpanFull(),
                        TextInput::make('company_phone')->label('No. Telepon / WA'),
                        TextInput::make('company_email')->label('Email')->email(),
                        FileUpload::make('company_logo')->label('Logo')->image()->directory('logos')->columnSpanFull(),
                        Textarea::make('invoice_notes')->label('Catatan Invoice (S&K)')->columnSpanFull(),
                    ])->columns(2),
                \Filament\Forms\Components\Section::make('Informasi Rekening Pembayaran')
                    ->schema([
                        Repeater::make('bank_accounts')
                            ->label('Daftar Rekening')
                            ->schema([
                                TextInput::make('bank_name')->label('Nama Bank')->required(),
                                TextInput::make('account_number')->label('No. Rekening')->required(),
                                TextInput::make('account_name')->label('Atas Nama')->required(),
                            ])
                            ->columns(3)
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = Setting::first();
        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }
        
        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
