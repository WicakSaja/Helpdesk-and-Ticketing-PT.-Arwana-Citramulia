<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua status menjadi lowercase
        $statusMappings = [
            'OPEN' => 'open',
            'Open' => 'open',
            'ASSIGNED' => 'assigned',
            'Assigned' => 'assigned',
            'IN PROGRESS' => 'in progress',
            'In Progress' => 'in progress',
            'RESOLVED' => 'resolved',
            'Resolved' => 'resolved',
            'CLOSED' => 'closed',
            'Closed' => 'closed',
            'WAITING' => 'waiting',
            'Waiting' => 'waiting',
        ];

        foreach ($statusMappings as $old => $new) {
            DB::table('ticket_statuses')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke format capitalized
        $statusMappings = [
            'open' => 'Open',
            'assigned' => 'Assigned',
            'in progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'waiting' => 'Waiting',
        ];

        foreach ($statusMappings as $old => $new) {
            DB::table('ticket_statuses')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }
};
