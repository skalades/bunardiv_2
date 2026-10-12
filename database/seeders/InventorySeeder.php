<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peralatan = [
            // Peralatan Makan & Minum
            ['kategori' => 'Peralatan Makan', 'nama' => 'Piring Makan Putih (Keramik)', 'satuan' => 'pcs', 'stok_aktual' => 500],
            ['kategori' => 'Peralatan Makan', 'nama' => 'Sendok Makan (Stainless)', 'satuan' => 'pcs', 'stok_aktual' => 500],
            ['kategori' => 'Peralatan Makan', 'nama' => 'Garpu Makan (Stainless)', 'satuan' => 'pcs', 'stok_aktual' => 500],
            ['kategori' => 'Peralatan Makan', 'nama' => 'Mangkok Sup', 'satuan' => 'pcs', 'stok_aktual' => 200],
            ['kategori' => 'Peralatan Minum', 'nama' => 'Gelas Goblet', 'satuan' => 'pcs', 'stok_aktual' => 300],
            ['kategori' => 'Peralatan Minum', 'nama' => 'Gelas Kaca Standar', 'satuan' => 'pcs', 'stok_aktual' => 400],

            // Peralatan Saji (Prasmanan)
            ['kategori' => 'Peralatan Saji', 'nama' => 'Chafing Dish Roll Top (Pemanas)', 'satuan' => 'unit', 'stok_aktual' => 20],
            ['kategori' => 'Peralatan Saji', 'nama' => 'Chafing Dish Kotak', 'satuan' => 'unit', 'stok_aktual' => 30],
            ['kategori' => 'Peralatan Saji', 'nama' => 'Sendok Sayur / Centong', 'satuan' => 'pcs', 'stok_aktual' => 50],
            ['kategori' => 'Peralatan Saji', 'nama' => 'Penjepit Makanan (Tong)', 'satuan' => 'pcs', 'stok_aktual' => 50],
            ['kategori' => 'Peralatan Saji', 'nama' => 'Nampan Stainless Besar', 'satuan' => 'pcs', 'stok_aktual' => 40],
            ['kategori' => 'Peralatan Saji', 'nama' => 'Dispenser Minuman Kaca', 'satuan' => 'unit', 'stok_aktual' => 10],

            // Mebel & Dekorasi
            ['kategori' => 'Mebel', 'nama' => 'Meja Bulat (Round Table)', 'satuan' => 'unit', 'stok_aktual' => 25],
            ['kategori' => 'Mebel', 'nama' => 'Meja Kotak (IBM)', 'satuan' => 'unit', 'stok_aktual' => 30],
            ['kategori' => 'Mebel', 'nama' => 'Kursi Futura', 'satuan' => 'unit', 'stok_aktual' => 300],
            ['kategori' => 'Mebel', 'nama' => 'Kursi Tiffany (Gold)', 'satuan' => 'unit', 'stok_aktual' => 150],
            ['kategori' => 'Mebel', 'nama' => 'Kursi Tiffany (Putih)', 'satuan' => 'unit', 'stok_aktual' => 150],
            ['kategori' => 'Dekorasi', 'nama' => 'Cover Kursi Futura (Putih)', 'satuan' => 'pcs', 'stok_aktual' => 300],
            ['kategori' => 'Dekorasi', 'nama' => 'Pita Kursi (Gold)', 'satuan' => 'pcs', 'stok_aktual' => 300],
            ['kategori' => 'Dekorasi', 'nama' => 'Taplak Meja Bulat (Putih)', 'satuan' => 'pcs', 'stok_aktual' => 30],
            ['kategori' => 'Dekorasi', 'nama' => 'Skirting Meja Prasmanan (Maroon)', 'satuan' => 'pcs', 'stok_aktual' => 15],
            ['kategori' => 'Dekorasi', 'nama' => 'Rangka Backdrop Besi Modular 3x6m', 'satuan' => 'set', 'stok_aktual' => 4],
            ['kategori' => 'Dekorasi', 'nama' => 'Standing Flower Besi Rustic', 'satuan' => 'unit', 'stok_aktual' => 12],
            ['kategori' => 'Dekorasi', 'nama' => 'Karpet Merah (Red Carpet 20m)', 'satuan' => 'roll', 'stok_aktual' => 6],
            ['kategori' => 'Dekorasi', 'nama' => 'Vas Bunga Kaca Meja VIP', 'satuan' => 'pcs', 'stok_aktual' => 40],

            // Sound, Lighting & Kelistrikan
            ['kategori' => 'Tata Suara & Lampu', 'nama' => 'Lampu Sorot Par LED RGB', 'satuan' => 'unit', 'stok_aktual' => 16],
            ['kategori' => 'Tata Suara & Lampu', 'nama' => 'Speaker Aktif Portable 15 Inch', 'satuan' => 'set', 'stok_aktual' => 4],
            ['kategori' => 'Tata Suara & Lampu', 'nama' => 'Mic Wireless Handheld', 'satuan' => 'set', 'stok_aktual' => 6],
            ['kategori' => 'Tata Suara & Lampu', 'nama' => 'Kabel Roll 25m', 'satuan' => 'pcs', 'stok_aktual' => 10],

            // Alat Pendukung Operasional Lapangan
            ['kategori' => 'Logistik', 'nama' => 'Box Container Plastik Besar', 'satuan' => 'unit', 'stok_aktual' => 40],
            ['kategori' => 'Logistik', 'nama' => 'Cooler Box (Es)', 'satuan' => 'unit', 'stok_aktual' => 5],
            ['kategori' => 'Logistik', 'nama' => 'Trolley Barang (Hand Truck)', 'satuan' => 'unit', 'stok_aktual' => 3],
            ['kategori' => 'Logistik', 'nama' => 'Kipas Misting Fan (Air)', 'satuan' => 'unit', 'stok_aktual' => 6],
            ['kategori' => 'Peralatan Kebersihan', 'nama' => 'Tempat Sampah Besar (Dustbin)', 'satuan' => 'unit', 'stok_aktual' => 10],
            ['kategori' => 'Peralatan Kebersihan', 'nama' => 'Kantong Plastik Sampah (Roll)', 'satuan' => 'roll', 'stok_aktual' => 50],
        ];

        foreach ($peralatan as $item) {
            Inventory::firstOrCreate(
                ['nama' => $item['nama']], 
                $item
            );
        }
    }
}
