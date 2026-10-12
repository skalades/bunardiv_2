<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_id', 'user_id', 'tipe_karyawan',
        'gaji_pokok', 'tunjangan_transport', 'tunjangan_makan',
        'upah_event', 'jumlah_hari_hadir', 'jumlah_event_hadir',
        'potongan_kasbon', 'potongan_lain',
        'total_bruto', 'total_bersih',
        'status', 'tanggal_dibayar', 'metode_bayar', 'catatan',
    ];

    protected $casts = [
        'tanggal_dibayar'     => 'date',
        'gaji_pokok'          => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_makan'     => 'decimal:2',
        'upah_event'          => 'decimal:2',
        'potongan_kasbon'     => 'decimal:2',
        'potongan_lain'       => 'decimal:2',
        'total_bruto'         => 'decimal:2',
        'total_bersih'        => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanPayments(): HasMany
    {
        return $this->hasMany(EmployeeLoanPayment::class);
    }
}
