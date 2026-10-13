<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'invoice_id', 'amount', 'payment_method', 'payment_date', 'reference_number', 'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (Payment $payment) {
            $payment->syncOrderStatus();
        });

        static::deleted(function (Payment $payment) {
            $payment->syncOrderStatus();
        });
    }

    public function syncOrderStatus(): void
    {
        if (! $this->order_id) {
            return;
        }

        $order = $this->order;
        if (! $order) {
            return;
        }

        if ($order->status === 'dibatalkan') {
            return;
        }

        $totalPaid = (float) $order->payments()->sum('amount');
        if ($totalPaid >= (float) $order->total && (float) $order->total > 0) {
            $order->updateQuietly(['status' => 'selesai']);
        } elseif ($totalPaid > 0) {
            if ($order->status === 'pesanan masuk') {
                $order->updateQuietly(['status' => 'dp dibayar']);
            }
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
