<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pic_id',
        'spk_number',
        'surat_jalan_number',
        'tanggal_persiapan',
        'status',
        'catatan',
    ];

    protected static function booted()
    {
        static::creating(function ($workOrder) {
            if (empty($workOrder->spk_number)) {
                $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
                $workOrder->spk_number = 'SPK-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            if (empty($workOrder->surat_jalan_number)) {
                $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
                $workOrder->surat_jalan_number = 'SJ-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(WorkOrderEquipment::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(WorkOrderUser::class);
    }
}
