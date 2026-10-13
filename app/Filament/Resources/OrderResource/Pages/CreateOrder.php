<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'POS Cepat — Kasir Restoran';
    }

    public function getHeading(): string
    {
        return 'POS Cepat — Kasir Restoran';
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Concurrency-safe NO ORDER generation
            $today = date('Ymd');
            $lastOrder = Order::where('no_order', 'like', "ORD-{$today}-%")->latest('id')->first();
            $nextSeq = 1;
            if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->no_order, $matches)) {
                $nextSeq = (int) $matches[1] + 1;
            } else {
                $count = Order::whereDate('created_at', now()->toDateString())->count();
                $nextSeq = $count + 1;
            }
            $noOrder = sprintf('ORD-%s-%04d', $today, $nextSeq);

            while (Order::where('no_order', $noOrder)->exists()) {
                $nextSeq++;
                $noOrder = sprintf('ORD-%s-%04d', $today, $nextSeq);
            }

            $customer_id = $data['customer_id'] ?? null;

            if ($customer_id === 'new') {
                $customer = Customer::create([
                    'nama' => $data['new_customer_nama'],
                    'no_telp' => $data['new_customer_no_wa'] ?? null,
                    'alamat' => $data['new_customer_alamat'] ?? null,
                ]);
                $customer_id = $customer->id;
            }

            $order = static::getModel()::create([
                'no_order' => $noOrder,
                'customer_id' => $customer_id,
                'nama_acara' => $data['nama_acara'],
                'jenis_acara' => $data['jenis_acara'],
                'tanggal' => $data['tanggal'],
                'jam' => $data['jam'],
                'venue' => $data['venue'],
                'catatan_khusus' => $data['catatan_khusus'],
                'diskon' => $data['diskon'] ?? 0,
                'biaya_tambahan' => $data['biaya_tambahan'] ?? 0,
                'subtotal' => $data['subtotal'] ?? 0,
                'total' => $data['total'] ?? 0,
                'status' => 'pesanan masuk',
            ]);

            $cartItems = json_decode($data['cart_data'] ?? '[]', true);
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'harga' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);
                }
            }

            return $order;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
