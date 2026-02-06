<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generate dummy users dengan roles:
     * - 20 Requester
     * - 5 Technician
     * - 3 Helpdesk
     * 
     * Password untuk semua user: password123
     */
    public function run(): void
    {
        $password = Hash::make('password123');
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('Departments not found! Run DepartmentSeeder first.');
            return;
        }

        // ====================================
        // HELPDESK USERS (3)
        // ====================================
        $helpdeskUsers = [
            ['name' => 'Helpdesk Satu', 'email' => 'helpdesk1@arwana.com', 'phone' => '081234567801'],
            ['name' => 'Helpdesk Dua', 'email' => 'helpdesk2@arwana.com', 'phone' => '081234567802'],
            ['name' => 'Helpdesk Tiga', 'email' => 'helpdesk3@arwana.com', 'phone' => '081234567803'],
        ];

        foreach ($helpdeskUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $password,
                'department_id' => $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('helpdesk');
            $this->command->info("Created helpdesk: {$userData['name']}");
        }

        // ====================================
        // TECHNICIAN USERS (5)
        // ====================================
        $technicianUsers = [
            ['name' => 'Ahmad Teknisi', 'email' => 'tech1@arwana.com', 'phone' => '081234567811'],
            ['name' => 'Budi Repair', 'email' => 'tech2@arwana.com', 'phone' => '081234567812'],
            ['name' => 'Candra IT', 'email' => 'tech3@arwana.com', 'phone' => '081234567813'],
            ['name' => 'Dedi Network', 'email' => 'tech4@arwana.com', 'phone' => '081234567814'],
            ['name' => 'Eka Support', 'email' => 'tech5@arwana.com', 'phone' => '081234567815'],
        ];

        foreach ($technicianUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => $password,
                'department_id' => $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('technician');
            $this->command->info("Created technician: {$userData['name']}");
        }

        // ====================================
        // REQUESTER USERS (20)
        // ====================================
        $requesterNames = [
            'Fahmi Produksi', 'Gina Office', 'Hadi Security', 'Indah Admin',
            'Joko Operator', 'Kiki Manager', 'Lina Staff', 'Made Supervisor',
            'Nanda Clerk', 'Omar Coordinator', 'Putri Analyst', 'Qori Assistant',
            'Rina Finance', 'Sandi Marketing', 'Tuti HR', 'Umar Sales',
            'Vina Accounting', 'Wawan Logistic', 'Yuni Quality', 'Zaki Warehouse'
        ];

        foreach ($requesterNames as $index => $name) {
            $slug = strtolower(str_replace(' ', '', $name));
            $user = User::create([
                'name' => $name,
                'email' => "requester{$index}@arwana.com",
                'phone' => '0812345678' . str_pad($index + 20, 2, '0', STR_PAD_LEFT),
                'password' => $password,
                'department_id' => $departments->random()->id,
                'is_active' => true,
            ]);
            $user->assignRole('requester');
            $this->command->info("Created requester: {$name}");
        }

        $this->command->info('✅ Dummy users created successfully!');
        $this->command->info('📧 Email/Phone: Use any user above');
        $this->command->info('🔑 Password: password123');
    }
}
