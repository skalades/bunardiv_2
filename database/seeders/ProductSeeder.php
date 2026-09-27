<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['kategori' => 'CATERING', 'nama' => 'Paket Catering Basic', 'harga' => 45000],
            ['kategori' => 'CATERING', 'nama' => 'Paket Catering Premium', 'harga' => 75000],
            ['kategori' => 'CATERING', 'nama' => 'Paket Prasmanan Deluxe', 'harga' => 95000],
            ['kategori' => 'CATERING', 'nama' => 'Tambahan Menu Ayam Bakar', 'harga' => 15000],
            ['kategori' => 'CATERING', 'nama' => 'Snack Box', 'harga' => 12000],
            ['kategori' => 'DECORATION', 'nama' => 'Backdrop Bunga Rustic', 'harga' => 5000000],
            ['kategori' => 'DECORATION', 'nama' => 'Backdrop Klasik Gold', 'harga' => 6500000],
            ['kategori' => 'DECORATION', 'nama' => 'Meja & Kursi Tiffany (per pax)', 'harga' => 25000],
            ['kategori' => 'DECORATION', 'nama' => 'Lighting LED Package', 'harga' => 1500000],
            ['kategori' => 'DECORATION', 'nama' => 'Sound System', 'harga' => 2000000],
            ['kategori' => 'DECORATION', 'nama' => 'Bunga Segar per Meja', 'harga' => 150000],
        ];

        foreach ($products as $p) {
            \App\Models\Product::updateOrCreate(['nama' => $p['nama']], $p);
        }
    }
}
