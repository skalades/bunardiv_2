<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'no_invoice', 'order_id', 'judul_tagihan', 'nominal_tagihan', 'jatuh_tempo'
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
