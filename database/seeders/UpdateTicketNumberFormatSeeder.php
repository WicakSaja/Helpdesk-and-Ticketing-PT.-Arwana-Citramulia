<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;

class UpdateTicketNumberFormatSeeder extends Seeder
{
    public function run()
    {
        // Update existing tickets dengan format baru
        $tickets = Ticket::all();
        
        foreach ($tickets as $ticket) {
            $year = date('Y');
            $number = str_pad($ticket->id, 6, '0', STR_PAD_LEFT);
            $newTicketNumber = "TKT-{$year}-{$number}";
            
            $ticket->update(['ticket_number' => $newTicketNumber]);
        }
        
        echo "Updated " . $tickets->count() . " tickets to new format\n";
    }
}
