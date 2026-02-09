<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Generate dummy users:
     * - 3 akun utama (helpdesk, technician, requester)
     * - 2 tambahan per role
     *
     * Password semua: 12345678
     */
    public function run(): void
    {
        $password = Hash::make('12345678');
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('Departments not found! Run DepartmentSeeder first.');
            return;
        }

        // ====================================
        // HELPDESK USERS (3)
        // ====================================
        $helpdeskUsers = [
            ['name' => 'Helpdesk Utama', 'email' => 'helpdesk@arwanacitra.com', 'phone' => '081200000001'],
            ['name' => 'Siti Helpdesk', 'email' => 'siti.helpdesk@arwanacitra.com', 'phone' => '081200000002'],
            ['name' => 'Andi Helpdesk', 'email' => 'andi.helpdesk@arwanacitra.com', 'phone' => '081200000003'],
        ];

        foreach ($helpdeskUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $password,
                'department_id' => $departments->where('name', 'office')->first()->id ?? $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('helpdesk');
            $this->command->info("Created helpdesk: {$userData['name']}");
        }

        // ====================================
        // TECHNICIAN USERS (3)
        // ====================================
        $technicianUsers = [
            ['name' => 'Teknisi Utama', 'email' => 'technician@arwanacitra.com', 'phone' => '081200000011'],
            ['name' => 'Budi Teknisi', 'email' => 'budi.teknisi@arwanacitra.com', 'phone' => '081200000012'],
            ['name' => 'Rudi Teknisi', 'email' => 'rudi.teknisi@arwanacitra.com', 'phone' => '081200000013'],
        ];

        foreach ($technicianUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $password,
                'department_id' => $departments->where('name', 'office')->first()->id ?? $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('technician');
            $this->command->info("Created technician: {$userData['name']}");
        }

        // ====================================
        // REQUESTER USERS (3)
        // ====================================
        $requesterUsers = [
            ['name' => 'Requester Utama', 'email' => 'requester@arwanacitra.com', 'phone' => '081200000021', 'dept' => 'produksi'],
            ['name' => 'Dewi Produksi', 'email' => 'dewi.produksi@arwanacitra.com', 'phone' => '081200000022', 'dept' => 'produksi'],
            ['name' => 'Eko Security', 'email' => 'eko.security@arwanacitra.com', 'phone' => '081200000023', 'dept' => 'security'],
        ];

        foreach ($requesterUsers as $userData) {
            $dept = $departments->where('name', $userData['dept'])->first();
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $password,
                'department_id' => $dept->id ?? $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('requester');
            $this->command->info("Created requester: {$userData['name']}");
        }

        $this->command->info('');
        $this->command->info('Dummy users created successfully!');
        $this->command->info('Password: 12345678');
        $this->command->info('Akun utama:');
        $this->command->info('  helpdesk@arwanacitra.com');
        $this->command->info('  technician@arwanacitra.com');
        $this->command->info('  requester@arwanacitra.com');
    }
}
