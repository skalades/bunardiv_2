<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Seeder tidak bisa dijalankan karena tabel Customer atau Product kosong.');
            return;
        }

        $acaras = ['Pernikahan', 'Ulang Tahun', 'Khitanan', 'Rapat Tahunan', 'Gathering Kantor'];
        $venues = ['Gedung Serbaguna', 'Hotel Bintang 5', 'Rumah Pribadi', 'Aula Kampus', 'Taman Terbuka'];
        $statuses = ['pesanan masuk', 'dp dibayar', 'selesai'];

        // Create 10 dummy orders
        for ($i = 1; $i <= 10; $i++) {
            $customer = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $acara = $acaras[array_rand($acaras)];

            // Randomly select 2-4 products for this order
            $orderProducts = $products->random(rand(2, 4));

            $subtotal = 0;
            $items = [];

            foreach ($orderProducts as $product) {
                $qty = rand(10, 100);
                $itemSubtotal = $product->harga * $qty;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'harga' => $product->harga,
                    'qty' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $diskon = rand(0, 1) === 1 ? rand(50000, 200000) : 0;
            $biaya_tambahan = rand(0, 1) === 1 ? rand(100000, 500000) : 0;
            $total = $subtotal - $diskon + $biaya_tambahan;

            $order = Order::create([
                'no_order' => 'ORD-' . date('Ym') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT) . strtoupper(Str::random(3)),
                'customer_id' => $customer->id,
                'nama_acara' => 'Acara ' . $acara . ' ' . $customer->nama,
                'jenis_acara' => $acara,
                'tanggal' => now()->addDays(rand(1, 60))->toDateString(),
                'jam' => sprintf('%02d:00:00', rand(8, 19)),
                'venue' => $venues[array_rand($venues)],
                'catatan_khusus' => 'Pesanan digenerate dari seeder.',
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'biaya_tambahan' => $biaya_tambahan,
                'total' => $total,
                'status' => $status,
            ]);

            foreach ($items as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }

        $this->command->info('10 Data Order berhasil ditambahkan!');
    }
}
