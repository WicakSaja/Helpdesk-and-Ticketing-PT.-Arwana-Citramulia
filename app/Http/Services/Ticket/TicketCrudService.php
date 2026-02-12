<?php

namespace App\Http\Services\Ticket;

use App\Models\Ticket;
use Carbon\Carbon;
use App\Models\TicketAssignment;
use App\Models\TicketLog;
use App\Models\TicketSolution;
use App\Models\TicketStatus;
use App\Models\TechnicianTicketHistory;
use App\Models\User;
use App\Notifications\NewTicketNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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

        $this->logAction(
            $ticket->id,
            $requesterId,
            'open',
            'Ticket dibuat dan berstatus open'
        );

        // Kirim notifikasi ke semua helpdesk aktif
        $this->notifyHelpdesk($ticket);

        return $ticket;
    }

    /**
     * Close ticket
     */
    public function closeTicket(Ticket $ticket, int $userId): Ticket
    {
        $ticket->update([
            'status_id' => TicketStatus::where('name', 'closed')->first()->id,
            'closed_at' => now(),
        ]);

        $logMessage = ($userId === $ticket->requester_id) 
            ? 'Ticket ditutup oleh requester' 
            : 'Ticket ditutup oleh helpdesk';

        $this->logAction(
            $ticket->id,
            $userId,
            'closed',
            $logMessage
        );

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

        $this->logAction(
            $ticket->id,
            $assignerId,
            'assigned',
            'Ticket di-assign ke technician'
        );

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

        $this->logAction(
            $ticket->id,
            $technicianId,
            'in_progress',
            'Ticket dikonfirmasi dan dikerjakan oleh technician'
        );

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

        $this->logAction(
            $ticket->id,
            $technicianId,
            'rejected',
            'Ticket ditolak oleh technician dan kembali ke open'
        );

        return ['ticket' => $ticket];
    }

    /**
     * Helpdesk unresolves ticket
     */
    public function unresolveTicket(Ticket $ticket, int $userId): array
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

        $this->logAction(
            $ticket->id,
            $userId,
            'unresolved',
            'Ticket di-unresolve dan dikembalikan ke in progress'
        );

        return ['ticket' => $ticket];
    }

    /**
     * Technician solves ticket
     */
    public function solveTicket(Ticket $ticket, int $technicianId, string $solutionText, string $resolvedAt): array
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

        $resolvedAtTime = Carbon::createFromFormat('Y-m-d H:i:s', $resolvedAt, config('app.timezone'));

        DB::transaction(function () use ($ticket, $technicianId, $solutionText, $resolvedAtTime) {
            TicketSolution::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'solution_text' => $solutionText,
                    'solved_by' => $technicianId,
                    'solved_at' => $resolvedAtTime,
                ]
            );

            TechnicianTicketHistory::create([
                'ticket_id' => $ticket->id,
                'technician_id' => $technicianId,
                'resolved_at' => $resolvedAtTime,
                'solution_text' => $solutionText,
            ]);

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'resolved')->firstOrFail()->id,
            ]);
        });

        $this->logAction(
            $ticket->id,
            $technicianId,
            'resolved',
            'Ticket diselesaikan oleh technician'
        );

        return ['ticket' => $ticket];
    }

    private function logAction(int $ticketId, int $userId, string $action, ?string $description = null): void
    {
        TicketLog::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
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

    /**
     * Kirim notifikasi ticket baru ke semua helpdesk aktif.
     */
    private function notifyHelpdesk(Ticket $ticket): void
    {
        if (!config('ticketnotification.enabled')) {
            return;
        }

        $ticket->loadMissing(['requester', 'category', 'status']);

        $helpdeskUsers = User::role('helpdesk')
            ->where('is_active', true)
            ->get();

        if ($helpdeskUsers->isNotEmpty()) {
            Notification::send($helpdeskUsers, new NewTicketNotification($ticket));
        }
    }
}
