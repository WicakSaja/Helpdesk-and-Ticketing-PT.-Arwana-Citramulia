<?php

namespace App\Http\Services\Dashboard;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;

class RequesterDashboard
{
    private User $requester;

    public function __construct(User $requester)
    {
        $this->requester = $requester;
    }

    /**
     * Get dashboard data for requester
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'my_tickets' => $this->getMyTickets(),
        ];
    }

    /**
     * Get summary data for requester
     */
    private function getSummary(): array
    {
        $openStatusId = TicketStatus::where('name', 'open')->value('id');
        $assignedStatusId = TicketStatus::where('name', 'assigned')->value('id');
        $inProgressStatusId = TicketStatus::where('name', 'in progress')->value('id');
        $resolvedStatusId = TicketStatus::where('name', 'resolved')->value('id');
        $closedStatusId = TicketStatus::where('name', 'closed')->value('id');

        return [
            'total' => Ticket::where('requester_id', $this->requester->id)->count(),
            'open' => Ticket::where('requester_id', $this->requester->id)
                ->where('status_id', $openStatusId)
                ->count(),
            'process' => Ticket::where('requester_id', $this->requester->id)
                ->whereIn('status_id', [$assignedStatusId, $inProgressStatusId])
                ->count(),
            'solved' => Ticket::where('requester_id', $this->requester->id)
                ->whereIn('status_id', [$resolvedStatusId, $closedStatusId])
                ->count(),
        ];
    }

    /**
     * Get my tickets (last 5 created by this requester)
     */
    private function getMyTickets(): array
    {
        return Ticket::where('requester_id', $this->requester->id)
            ->with([
                'status:id,name,created_at,updated_at',
                'category:id,name,description,created_at,updated_at',
                'requester:id,name,email,department_id',
                'requester.department',
                'assignment' => function ($q) {
                    $q->with('technician:id,name,email');
                },
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'requester_id' => $ticket->requester_id,
                    'status_id' => $ticket->status_id,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'channel' => $ticket->channel,
                    'closed_at' => $ticket->closed_at,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'category_id' => $ticket->category_id,
                    'status' => $ticket->status ? [
                        'id' => $ticket->status->id,
                        'name' => $ticket->status->name,
                        'created_at' => $ticket->status->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $ticket->status->updated_at->format('Y-m-d H:i:s'),
                    ] : null,
                    'category' => $ticket->category ? [
                        'id' => $ticket->category->id,
                        'name' => $ticket->category->name,
                        'description' => $ticket->category->description,
                        'created_at' => $ticket->category->created_at,
                        'updated_at' => $ticket->category->updated_at,
                    ] : null,
                    'requester' => $ticket->requester ? [
                        'id' => $ticket->requester->id,
                        'name' => $ticket->requester->name,
                        'email' => $ticket->requester->email,
                        'department' => $ticket->requester->department ? [
                            'id' => $ticket->requester->department->id,
                            'name' => $ticket->requester->department->name,
                        ] : null,
                    ] : null,
                    'assignment' => $ticket->assignment ? [
                        'id' => $ticket->assignment->id,
                        'ticket_id' => $ticket->assignment->ticket_id,
                        'assigned_to' => $ticket->assignment->assigned_to,
                        'assigned_by' => $ticket->assignment->assigned_by,
                        'assigned_at' => $ticket->assignment->assigned_at,
                        'notes' => $ticket->assignment->notes,
                        'technician' => $ticket->assignment->technician ? [
                            'id' => $ticket->assignment->technician->id,
                            'name' => $ticket->assignment->technician->name,
                            'email' => $ticket->assignment->technician->email,
                        ] : null,
                    ] : null,
                ];
            })
            ->toArray();
    }
}
