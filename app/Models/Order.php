<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'no_order', 'customer_id', 'nama_acara', 'jenis_acara', 'tanggal', 'jam',
        'venue', 'catatan_khusus', 'subtotal', 'diskon', 'biaya_tambahan', 'total', 'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function crews()
    {
        return $this->belongsToMany(User::class, 'order_crew')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
