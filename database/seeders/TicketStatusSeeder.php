<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketStatus;
use Carbon\Carbon;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        TicketStatus::insert([
            [
                'name' => 'Open',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'In Progress',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Resolved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Closed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

