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
                'name' => 'open',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'in progress',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'assigned',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'closed',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'waiting',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'resolved',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

