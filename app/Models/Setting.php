<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name', 'company_address', 'company_phone', 'company_email', 'company_logo', 'bank_accounts', 'invoice_notes'
    ];

    protected $casts = [
        'bank_accounts' => 'array',
    ];

    /**
     * Dapatkan instance Setting saat ini atau instance default jika belum dikonfigurasi.
     */
    public static function getSettings(): self
    {
        return static::first() ?? new static([
            'company_name'    => 'N7 DECORATION & CATERING BU NARDI',
            'company_address' => 'Basecamp Logistik & Dapur Utama, Jakarta Barat',
            'company_phone'   => '0812-8899-7700',
            'company_email'   => 'operasional@n7bunardi.com',
            'bank_accounts'   => [
                ['bank_name' => 'BCA', 'account_number' => '5420-9988-77', 'account_name' => 'BU NARDI CATERING'],
                ['bank_name' => 'Bank Mandiri', 'account_number' => '120-00-9988776-5', 'account_name' => 'BU NARDI CATERING'],
            ],
            'invoice_notes'   => "1. Pembayaran dianggap sah jika dana telah efektif masuk ke rekening resmi kami.\n2. Pelunasan sisa tagihan wajib diselesaikan maksimal H-3 sebelum pelaksanaan acara.\n3. Konfirmasi perubahan jadwal atau menu wajib disampaikan secara tertulis maksimal H-7.",
        ]);
    }

    /**
     * Dapatkan path absolut file logo di disk jika ada.
     */
    public function getLogoPath(): ?string
    {
        if (empty($this->company_logo)) {
            return null;
        }

        $candidates = [
            storage_path('app/public/' . $this->company_logo),
            public_path('storage/' . $this->company_logo),
            public_path($this->company_logo),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Dapatkan daftar rekening bank resmi yang selalu valid (array).
     */
    public function getFormattedBankAccounts(): array
    {
        if (!empty($this->bank_accounts) && is_array($this->bank_accounts)) {
            return $this->bank_accounts;
        }

        return [
            ['bank_name' => 'BCA', 'account_number' => '5420-9988-77', 'account_name' => $this->company_name ?: 'BU NARDI CATERING'],
            ['bank_name' => 'Bank Mandiri', 'account_number' => '120-00-9988776-5', 'account_name' => $this->company_name ?: 'BU NARDI CATERING'],
        ];
    }
}
