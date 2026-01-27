<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Hardware',
                'description' => 'Masalah perangkat keras seperti komputer, printer, scanner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Software',
                'description' => 'Masalah aplikasi, OS, dan sistem internal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Network',
                'description' => 'Masalah jaringan, internet, WiFi, VPN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Account & Access',
                'description' => 'Permintaan akun, reset password, akses sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'description' => 'Permasalahan lain-lain',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
