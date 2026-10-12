<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'user_id',
        'peran',
        'hadir',
        'waktu_absen',
        'upah_dibayar',
        'catatan_kehadiran',
    ];

    protected $casts = [
        'hadir'        => 'boolean',
        'waktu_absen'  => 'datetime',
        'upah_dibayar' => 'decimal:2',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
