<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'user_id', 'jumlah', 'sisa_hutang',
        'tanggal_pengajuan', 'tanggal_disetujui',
        'status', 'metode_cicilan', 'cicilan_per_bulan',
        'disetujui_oleh', 'catatan',
    ];

    protected $casts = [
        'tanggal_pengajuan'  => 'date',
        'tanggal_disetujui'  => 'date',
        'jumlah'             => 'decimal:2',
        'sisa_hutang'        => 'decimal:2',
        'cicilan_per_bulan'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EmployeeLoanPayment::class);
    }

    public function isLunas(): bool
    {
        return $this->sisa_hutang <= 0;
    }
}
