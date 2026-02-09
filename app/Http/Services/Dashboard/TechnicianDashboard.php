<?php

namespace App\Http\Services\Dashboard;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TechnicianTicketHistory;
use Carbon\Carbon;

class TechnicianDashboard
{
    private User $technician;

    public function __construct(User $technician)
    {
        $this->technician = $technician;
    }

    /**
     * Get dashboard data for technician
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'my_tickets' => $this->getMyTickets(),
        ];
    }

    /**
     * Get summary data for technician
     */
    private function getSummary(): array
    {
        $assignedStatusId = TicketStatus::where('name', 'assigned')->value('id');
        $inProgressStatusId = TicketStatus::where('name', 'in progress')->value('id');
        $today = Carbon::now()->startOfDay();

        return [
            'total' => TechnicianTicketHistory::where('technician_id', $this->technician->id)->count(),
            'assigned' => Ticket::whereHas('assignment', function ($q) {
                $q->where('assigned_to', $this->technician->id);
            })
            ->where('status_id', $assignedStatusId)
            ->count(),
            'in_progress' => Ticket::whereHas('assignment', function ($q) {
                $q->where('assigned_to', $this->technician->id);
            })
            ->where('status_id', $inProgressStatusId)
            ->count(),
            'solved_today' => TechnicianTicketHistory::where('technician_id', $this->technician->id)
                ->whereDate('resolved_at', $today)
                ->count(),
        ];
    }

    /**
     * Get my tickets (assigned to this technician, excluding Closed)
     */
    private function getMyTickets(): array
    {
        $StatusId = TicketStatus::where('name', 'assigned')->value('id');

        return Ticket::whereHas('assignment', function ($q) {
            $q->where('assigned_to', $this->technician->id);
        })
        ->where('status_id', $StatusId)
        ->with([
            'requester:id,name,email,department_id',
            'requester.department',
            'status:id,name',
            'category:id,name',
        ])
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'requester' => $ticket->requester ? $ticket->requester->name : 'Unknown',
                'requester_department' => $ticket->requester && $ticket->requester->department ? $ticket->requester->department->name : null,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'category' => $ticket->category ? $ticket->category->name : 'Unknown',
                'status' => $ticket->status ? $ticket->status->name : 'Unknown',
                'created_at' => $ticket->created_at->format('Y-m-d H:i'),
            ];
        })
        ->toArray();
    }
}
