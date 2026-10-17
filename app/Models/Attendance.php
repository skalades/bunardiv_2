<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'status',
        'jam_masuk',
        'jam_pulang',
        'denda',
        'keterangan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'denda' => 'decimal:2',
    ];

    public function setTanggalAttribute($value): void
    {
        $this->attributes['tanggal'] = $value ? Carbon::parse($value)->toDateString() : null;
    }

    public function getJamKerjaAttribute(): ?string
    {
        if (! $this->jam_masuk && ! $this->jam_pulang) {
            return null;
        }

        $masuk = $this->jam_masuk ? substr((string) $this->jam_masuk, 0, 5) : '-';
        $pulang = $this->jam_pulang ? substr((string) $this->jam_pulang, 0, 5) : '-';

        return "{$masuk} - {$pulang}";
    }

    public static function hitungDendaTelat(?string $jamMasuk, string $jamJadwal = '08:00', float $tarifPer30Menit = 5000): float
    {
        if (! $jamMasuk) {
            return 0;
        }

        try {
            $masuk = Carbon::createFromFormat('H:i', substr($jamMasuk, 0, 5));
            $jadwal = Carbon::createFromFormat('H:i', substr($jamJadwal, 0, 5));

            if ($masuk->greaterThan($jadwal)) {
                $menitTelat = $jadwal->diffInMinutes($masuk);
                $kelipatan = (int) ceil($menitTelat / 30);

                return $kelipatan * $tarifPer30Menit;
            }
        } catch (\Throwable $e) {
            return 0;
        }

        return 0;
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

