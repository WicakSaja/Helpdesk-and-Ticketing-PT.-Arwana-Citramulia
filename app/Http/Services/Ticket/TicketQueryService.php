<?php

namespace App\Http\Services\Ticket;

use App\Models\Ticket;
use App\Models\User;

class TicketQueryService
{
    /**
     * Get tickets for index endpoint
     */
    public function listTickets(User $user)
    {
        return Ticket::with(['status', 'category', 'requester.department'])
            ->when($user->hasRole('requester'), fn ($q) =>
                $q->where('requester_id', $user->id)
            )
            ->when($user->hasRole('technician'), fn ($q) =>
                $q->whereHas('assignments', fn ($a) =>
                    $a->where('technician_id', $user->id)
                )
            )
            ->latest()
            ->get();
    }

    /**
     * Tickets created by requester
     */
    public function myTickets(User $user)
    {
        return Ticket::where('requester_id', $user->id)
            ->with(['status', 'category', 'requester:id,name,email,department_id', 'requester.department', 'assignment.technician:id,name,email'])
            ->latest()
            ->get();
    }

    /**
     * Ticket detail with relations
     */
    public function getTicketDetail(Ticket $ticket): Ticket
    {
        $ticket->load([
            'requester:id,name,email,department_id',
            'requester.department',
            'category:id,name',
            'status:id,name',
            'assignment.technician:id,name,email',
            'assignment.assigner:id,name,email',
            'solution',
            'technicianHistories.technician:id,name,email',
        ]);

        return $ticket;
    }

    /**
     * Ticket completion history
     */
    public function completionHistory(Ticket $ticket): Ticket
    {
        $ticket->load(['technicianHistories.technician:id,name,email']);

        return $ticket;
    }

    /**
     * Get all tickets by status
     * Query parameter: status (contoh: open, assigned, in progress, resolved, closed)
     */
    public function byStatus(string $status, User $user)
    {
        return Ticket::whereHas('status', fn ($q) =>
            $q->where('name', strtolower($status))
        )
        ->with(['status', 'category', 'requester:id,name,email,department_id', 'requester.department', 'assignment.technician:id,name,email'])
        ->when($user->hasRole('requester'), fn ($q) =>
            $q->where('requester_id', $user->id)
        )
        ->when($user->hasRole('technician'), fn ($q) =>
            $q->whereHas('assignments', fn ($a) =>
                $a->where('technician_id', $user->id)
            )
        )
        ->latest()
        ->get();
    }
}
