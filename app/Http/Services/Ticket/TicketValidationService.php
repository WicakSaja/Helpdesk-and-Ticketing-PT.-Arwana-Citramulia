<?php

namespace App\Http\Services\Ticket;

use App\Models\Ticket;

class TicketValidationService
{
    /**
     * Validate if ticket can be assigned (status must be Open)
     */
    public function canAssign(Ticket $ticket): array
    {
        if ($ticket->status->name !== 'Open') {
            return ['valid' => false, 'message' => 'Ticket must be in Open status to be assigned'];
        }
        return ['valid' => true];
    }

    /**
     * Validate if ticket can be confirmed (must be Assigned and owned by technician)
     */
    public function canConfirm(Ticket $ticket, int $technicianId): array
    {
        if (!$ticket->assignment) {
            return ['valid' => false, 'message' => 'Ticket has not been assigned'];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['valid' => false, 'message' => 'You are not assigned to this ticket'];
        }

        if ($ticket->status->name !== 'Assigned') {
            return ['valid' => false, 'message' => 'Ticket is not in Assigned status'];
        }

        return ['valid' => true];
    }

    /**
     * Validate if ticket can be rejected
     */
    public function canReject(Ticket $ticket, int $technicianId): array
    {
        if (!$ticket->assignment) {
            return ['valid' => false, 'message' => 'Ticket has not been assigned'];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['valid' => false, 'message' => 'You are not assigned to this ticket'];
        }

        if ($ticket->status->name !== 'Assigned') {
            return ['valid' => false, 'message' => 'Ticket is not in Assigned status'];
        }

        return ['valid' => true];
    }

    /**
     * Validate if ticket can be solved
     */
    public function canSolve(Ticket $ticket, int $technicianId): array
    {
        if (!$ticket->assignment) {
            return ['valid' => false, 'message' => 'Ticket has not been assigned'];
        }

        if ($ticket->assignment->assigned_to !== $technicianId) {
            return ['valid' => false, 'message' => 'You are not assigned to this ticket'];
        }

        if ($ticket->status->name !== 'In Progress') {
            return ['valid' => false, 'message' => 'Ticket must be in In Progress status to be resolved'];
        }

        return ['valid' => true];
    }

    /**
     * Validate if ticket can be unresolved
     */
    public function canUnresolve(Ticket $ticket): array
    {
        if ($ticket->status->name !== 'Resolved') {
            return ['valid' => false, 'message' => 'Ticket is not in Resolved status'];
        }

        if (!$ticket->assignment) {
            return ['valid' => false, 'message' => 'Ticket has no assigned technician'];
        }

        return ['valid' => true];
    }

    /**
     * Validate if ticket can be closed
     */
    public function canClose(Ticket $ticket): array
    {
        if ($ticket->status->name !== 'Resolved') {
            return ['valid' => false, 'message' => 'Ticket must be in Resolved status to be closed'];
        }

        return ['valid' => true];
    }
}
