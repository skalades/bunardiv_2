<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['nama', 'no_telp', 'telepon', 'no_telepon', 'alamat'];

    public function setTeleponAttribute($value): void
    {
        $this->attributes['no_telp'] = $value;
    }

    public function setNoTeleponAttribute($value): void
    {
        $this->attributes['no_telp'] = $value;
    }
}
