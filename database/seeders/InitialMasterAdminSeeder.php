<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialMasterAdminSeeder extends Seeder
{
    public function run()
    {
        // Pastikan env ada
        if (!config('app.master_admin_email')) {
            $this->command->warn('Master admin env not set. Skipping seeder.');
            return;
        }

        $user = User::firstOrCreate(
            ['email' => config('app.master_admin_email')],
            [
                'name' => config('app.master_admin_name'),
                'phone' => config('app.master_admin_phone'),
                'password' => Hash::make(config('app.master_admin_password')),
            ]
        );

        if (!$user->hasRole('master-admin')) {
            $user->assignRole('master-admin');
        }
    }
}
