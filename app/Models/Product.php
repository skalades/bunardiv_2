<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['kategori', 'nama', 'harga'];

    protected $casts = [
        'harga' => 'decimal:2',
    ];
}
