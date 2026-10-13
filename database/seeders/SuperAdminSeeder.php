<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bunardi.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('bunardi07'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Super Admin berhasil dibuat/diupdate: admin@bunardi.com / bunardi07');
    }
}
