<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'harga', 'qty', 'subtotal'];

    protected $casts = [
        'harga' => 'decimal:2',
        'qty' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCatatanAttribute(): ?string
    {
        return $this->attributes['catatan'] ?? null;
    }
}
