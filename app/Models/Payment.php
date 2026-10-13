<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'invoice_id', 'amount', 'payment_method', 'payment_date', 'reference_number', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public static function getPaymentMethods(): array
    {
        $methods = [
            'Tunai / Cash' => 'Tunai / Cash',
            'Transfer BCA' => 'Transfer BCA',
            'Transfer Mandiri' => 'Transfer Mandiri',
            'Transfer BRI' => 'Transfer BRI',
            'Transfer BNI' => 'Transfer BNI',
            'Transfer BSI' => 'Transfer BSI',
            'Transfer BJB' => 'Transfer BJB',
        ];

        try {
            $setting = Setting::first();
            if ($setting && is_array($setting->bank_accounts)) {
                foreach ($setting->bank_accounts as $bank) {
                    if (! empty($bank['bank_name'])) {
                        $name = trim($bank['bank_name']);
                        $key = 'Transfer '.$name;
                        $methods[$key] = $key;
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        $methods['QRIS / E-Wallet'] = 'QRIS / E-Wallet';
        $methods['Kartu Kredit'] = 'Kartu Kredit';
        $methods['Lainnya'] = 'Lainnya';

        return $methods;
    }

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
