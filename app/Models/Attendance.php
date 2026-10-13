<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'status', 'keterangan', 'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function setTanggalAttribute($value): void
    {
        $this->attributes['tanggal'] = $value ? Carbon::parse($value)->toDateString() : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
