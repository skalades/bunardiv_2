<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = ['kategori', 'nama', 'satuan', 'stok_aktual'];

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Hitung estimasi stok berjalan sesuai PRD FR-7.
     * Jika stok_aktual null (belum opname), mengembalikan null atau bisa disesuaikan.
     */
    public function getStokBerjalanAttribute()
    {
        if (is_null($this->stok_aktual)) {
            return null;
        }

        $masuk = $this->transactions()->where('tipe', 'masuk')->sum('qty');
        $keluar = $this->transactions()->where('tipe', 'keluar')->sum('qty');

        return $this->stok_aktual + $masuk - $keluar;
    }
}
