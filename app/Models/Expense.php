<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'kategori',
        'judul',
        'nominal',
        'order_id',
        'metode_pembayaran',
        'pic_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Expense $expense) {
            if (empty($expense->nomor_transaksi)) {
                $datePrefix = Carbon::parse($expense->tanggal ?? now())->format('Ymd');
                $last = static::where('nomor_transaksi', 'like', "EXP-{$datePrefix}-%")->latest('id')->first();
                $seq = 1;
                if ($last && preg_match('/-(\d+)$/', $last->nomor_transaksi, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }
                $expense->nomor_transaksi = sprintf('EXP-%s-%04d', $datePrefix, $seq);
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
}
