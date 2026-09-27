<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['nama' => 'Bapak Budi', 'no_telp' => '081234567890'],
            ['nama' => 'Ibu Siti', 'no_telp' => '081298765432'],
            ['nama' => 'PT. Maju Mundur', 'no_telp' => '0218889999'],
        ];

        foreach ($customers as $c) {
            \App\Models\Customer::updateOrCreate(['nama' => $c['nama']], $c);
        }
    }
}
