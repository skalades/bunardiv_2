<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'no_order', 'customer_id', 'nama_acara', 'jenis_acara', 'tanggal', 'jam',
        'venue', 'catatan_khusus', 'subtotal', 'diskon', 'biaya_tambahan', 'total', 'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'biaya_tambahan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            $order->items()->delete();
            $order->payments()->delete();
            $order->invoices()->delete();
            foreach ($order->workOrders as $workOrder) {
                $workOrder->delete();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function crewAssignments()
    {
        return $this->hasManyThrough(WorkOrderUser::class, WorkOrder::class);
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
