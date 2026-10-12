<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id', 'nik', 'jabatan', 'tipe',
        'gaji_pokok', 'upah_per_event',
        'tunjangan_transport', 'tunjangan_makan',
        'no_hp', 'tanggal_bergabung', 'status_aktif',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'status_aktif'      => 'boolean',
        'gaji_pokok'        => 'decimal:2',
        'upah_per_event'    => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_makan'   => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class, 'user_id', 'user_id');
    }
}
