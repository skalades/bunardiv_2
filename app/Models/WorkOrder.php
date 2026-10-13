<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'tanggal_persiapan' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($workOrder) {
            $todayStr = now()->format('Ymd');
            if (empty($workOrder->spk_number)) {
                $last = static::where('spk_number', 'like', "SPK-{$todayStr}-%")->latest('id')->first();
                $seq = 1;
                if ($last && preg_match('/-(\d+)$/', $last->spk_number, $m)) {
                    $seq = (int) $m[1] + 1;
                }
                $workOrder->spk_number = sprintf('SPK-%s-%04d', $todayStr, $seq);
            }

            if (empty($workOrder->surat_jalan_number)) {
                $lastSj = static::where('surat_jalan_number', 'like', "SJ-{$todayStr}-%")->latest('id')->first();
                $seqSj = 1;
                if ($lastSj && preg_match('/-(\d+)$/', $lastSj->surat_jalan_number, $m)) {
                    $seqSj = (int) $m[1] + 1;
                }
                $workOrder->surat_jalan_number = sprintf('SJ-%s-%04d', $todayStr, $seqSj);
            }
        });

        static::deleting(function ($workOrder) {
            $workOrder->equipment()->delete();
            $workOrder->users()->delete();
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
