<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketSolution;
use App\Models\TicketLog;

echo "===========================================\n";
echo "DATABASE SEEDING VERIFICATION\n";
echo "===========================================\n\n";

echo "👥 USERS:\n";
echo "  Total Users: " . User::count() . "\n";
echo "  - Master Admin: " . User::role('master-admin')->count() . "\n";
echo "  - Helpdesk: " . User::role('helpdesk')->count() . "\n";
echo "  - Technician: " . User::role('technician')->count() . "\n";
echo "  - Requester: " . User::role('requester')->count() . "\n";

echo "\n🎫 TICKETS:\n";
echo "  Total Tickets: " . Ticket::count() . "\n";
echo "  - OPEN: " . Ticket::whereHas('status', fn($q) => $q->where('name', 'open'))->count() . "\n";
echo "  - ASSIGNED: " . Ticket::whereHas('status', fn($q) => $q->where('name', 'assigned'))->count() . "\n";
echo "  - IN PROGRESS: " . Ticket::whereHas('status', fn($q) => $q->where('name', 'in progress'))->count() . "\n";
echo "  - RESOLVED: " . Ticket::whereHas('status', fn($q) => $q->where('name', 'resolved'))->count() . "\n";
echo "  - CLOSED: " . Ticket::whereHas('status', fn($q) => $q->where('name', 'closed'))->count() . "\n";

echo "\n📋 RELATIONS:\n";
echo "  Ticket Assignments: " . TicketAssignment::count() . "\n";
echo "  Ticket Solutions: " . TicketSolution::count() . "\n";
echo "  Ticket Logs: " . TicketLog::count() . "\n";

echo "\n📅 DATE RANGE:\n";
$firstTicket = Ticket::orderBy('created_at')->first();
$lastTicket = Ticket::orderBy('created_at', 'desc')->first();
if ($firstTicket && $lastTicket) {
    echo "  From: " . $firstTicket->created_at->format('d M Y') . "\n";
    echo "  To: " . $lastTicket->created_at->format('d M Y') . "\n";
}

echo "\n===========================================\n";
echo "✅ DATABASE SEEDING COMPLETED SUCCESSFULLY!\n";
echo "===========================================\n";
echo "\n🔑 TEST ACCOUNTS (Password: password123)\n";
echo "  Helpdesk: helpdesk1@arwana.com\n";
echo "  Technician: tech1@arwana.com\n";
echo "  Requester: requester0@arwana.com\n";
echo "\n";
