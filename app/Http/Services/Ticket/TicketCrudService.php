<?php

namespace App\Http\Services\Ticket;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketSolution;
use App\Models\TicketStatus;
use App\Models\TechnicianTicketHistory;
use Illuminate\Support\Facades\DB;

class TicketCrudService
{
    /**
     * Create new ticket and generate ticket number
     */
    public function createTicket(array $validated, int $requesterId): Ticket
    {
        $status = TicketStatus::where('name', 'open')->firstOrFail();

        $ticket = Ticket::create([
            'ticket_number' => '',
            'requester_id' => $requesterId,
            'status_id' => $status->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'channel' => $validated['channel'],
        ]);

        $ticket->update([
            'ticket_number' => $this->generateTicketNumber($ticket->id)
        ]);

        return $ticket;
    }

    /**
     * Close ticket
     */
    public function closeTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status_id' => TicketStatus::where('name', 'closed')->first()->id,
            'closed_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Assign ticket to technician
     */
    public function assignTicket(Ticket $ticket, array $validated, int $assignerId): Ticket
    {
        DB::transaction(function () use ($ticket, $validated, $assignerId) {
            TicketAssignment::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'assigned_to' => $validated['assigned_to'],
                    'assigned_by' => $assignerId,
                    'assigned_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'assigned')->firstOrFail()->id
            ]);
        });

        $ticket->load('assignment.technician', 'status');

        return $ticket;
    }

    /**
     * Technician confirms assigned ticket
     */
    public function confirmTicket(Ticket $ticket, int $technicianId): array
    {
        $ticket->load(['assignment', 'status']);

        if (!$ticket->assignment) {
            return ['error' => 'Ticket has not been assigned', 'status' => 422];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['error' => 'You are not assigned to this ticket', 'status' => 403];
        }

        if ($ticket->status->name !== 'assigned') {
            return ['error' => 'Ticket is not in assigned status', 'status' => 422];
        }

        $ticket->update([
            'status_id' => TicketStatus::where('name', 'in progress')->firstOrFail()->id
        ]);

        return ['ticket' => $ticket];
    }

    /**
     * Technician rejects assigned ticket
     */
    public function rejectTicket(Ticket $ticket, int $technicianId): array
    {
        $ticket->load(['assignment', 'status']);

        if (!$ticket->assignment) {
            return ['error' => 'Ticket has not been assigned', 'status' => 422];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['error' => 'You are not assigned to this ticket', 'status' => 403];
        }

        if ($ticket->status->name !== 'assigned') {
            return ['error' => 'Ticket is not in assigned status', 'status' => 422];
        }

        DB::transaction(function () use ($ticket) {
            $ticket->assignment()->delete();

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'open')->firstOrFail()->id
            ]);
        });

        return ['ticket' => $ticket];
    }

    /**
     * Helpdesk unresolves ticket
     */
    public function unresolveTicket(Ticket $ticket): array
    {
        $ticket->load(['status', 'assignment']);

        if ($ticket->status->name !== 'resolved') {
            return ['error' => 'Ticket is not in resolved status', 'status' => 422];
        }

        if (!$ticket->assignment) {
            return ['error' => 'Ticket has no assigned technician', 'status' => 422];
        }

        $ticket->update([
            'status_id' => TicketStatus::where('name', 'in progress')->firstOrFail()->id
        ]);

        return ['ticket' => $ticket];
    }

    /**
     * Technician solves ticket
     */
    public function solveTicket(Ticket $ticket, int $technicianId, string $solutionText): array
    {
        $ticket->load(['assignment', 'status']);

        if (!$ticket->assignment) {
            return ['error' => 'Ticket has not been assigned', 'status' => 422];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['error' => 'You are not assigned to this ticket', 'status' => 403];
        }

        if ($ticket->status->name !== 'in progress') {
            return ['error' => 'Ticket must be in in progress status to be resolved', 'status' => 422];
        }

        DB::transaction(function () use ($ticket, $technicianId, $solutionText) {
            TicketSolution::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'solution_text' => $solutionText,
                    'solved_by' => $technicianId,
                    'solved_at' => now(),
                ]
            );

            TechnicianTicketHistory::create([
                'ticket_id' => $ticket->id,
                'technician_id' => $technicianId,
                'resolved_at' => now(),
                'solution_text' => $solutionText,
            ]);

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'resolved')->firstOrFail()->id,
            ]);
        });

        return ['ticket' => $ticket];
    }

    /**
     * Generate ticket number format TKT-YYYY-XXXXXX
     */
    private function generateTicketNumber(int $ticketId): string
    {
        $year = date('Y');
        $number = str_pad($ticketId, 5, '0', STR_PAD_LEFT);
        return "TKT-{$year}-{$number}";
    }
}
