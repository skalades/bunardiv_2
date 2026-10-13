<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $picId = $admin?->id ?? 1;
        $order = Order::first();

        $expenses = [
            [
                'tanggal' => '2026-10-02',
                'kategori' => 'Bahan Baku & Catering',
                'judul' => 'Belanja Daging Sapi 50kg & Ayam Fillet Pasar Induk',
                'nominal' => 6500000,
                'metode_pembayaran' => 'Transfer BCA',
                'order_id' => $order?->id,
                'pic_id' => $picId,
                'catatan' => 'Bahan prasmanan event pernikahan',
            ],
            [
                'tanggal' => '2026-10-03',
                'kategori' => 'Properti & Dekorasi',
                'judul' => 'Beli Bunga Mawar Putih, Baby Breath & Bunga Segar',
                'nominal' => 3200000,
                'metode_pembayaran' => 'Tunai / Cash',
                'order_id' => $order?->id,
                'pic_id' => $picId,
                'catatan' => 'Dekorasi pelaminan & aisle',
            ],
            [
                'tanggal' => '2026-10-05',
                'kategori' => 'Transport & Logistik',
                'judul' => 'BBM Truk Box Engkel & Tol Operasional Loading',
                'nominal' => 850000,
                'metode_pembayaran' => 'Tunai / Cash',
                'order_id' => $order?->id,
                'pic_id' => $picId,
                'catatan' => 'Pengiriman perlengkapan tenda dan catering ke venue',
            ],
            [
                'tanggal' => '2026-10-06',
                'kategori' => 'Konsumsi Tim',
                'judul' => 'Makan & Minum Kru Lapangan Dekorasi & Catering (15 Orang)',
                'nominal' => 600000,
                'metode_pembayaran' => 'Tunai / Cash',
                'order_id' => $order?->id,
                'pic_id' => $picId,
                'catatan' => 'Makan malam saat setting dekorasi H-1',
            ],
            [
                'tanggal' => '2026-10-08',
                'kategori' => 'Operasional & Kantor',
                'judul' => 'Tagihan Listrik Basecamp & WiFi Gudang Bulan Oktober',
                'nominal' => 1450000,
                'metode_pembayaran' => 'Transfer BCA',
                'order_id' => null,
                'pic_id' => $picId,
                'catatan' => 'Operasional rutin bulanan',
            ],
            [
                'tanggal' => '2026-10-10',
                'kategori' => 'Pemeliharaan Alat',
                'judul' => 'Laundry Cuci Kain Tenda & Taplak Meja 200 Pcs',
                'nominal' => 1200000,
                'metode_pembayaran' => 'Tunai / Cash',
                'order_id' => null,
                'pic_id' => $picId,
                'catatan' => 'Pencucian setelah event wedding',
            ],
            [
                'tanggal' => '2026-10-11',
                'kategori' => 'Bahan Baku & Catering',
                'judul' => 'Bumbu Dapur, Sayur Mayur & Minyak Goreng 5 Karton',
                'nominal' => 2100000,
                'metode_pembayaran' => 'Transfer Mandiri',
                'order_id' => null,
                'pic_id' => $picId,
                'catatan' => 'Restock gudang bahan kering',
            ],
        ];

        foreach ($expenses as $item) {
            Expense::create($item);
        }
    }
}
