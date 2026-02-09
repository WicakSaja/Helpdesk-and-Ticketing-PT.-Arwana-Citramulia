<?php

namespace App\Http\Services\Dashboard;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketStatus;
use Carbon\Carbon;

class HelpdeskDashboard
{
    /**
     * Get dashboard data for helpdesk
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'unassigned_tickets' => $this->getUnassignedTickets(),
            'ticket_trend' => $this->getTicketTrend(),
            'category_distribution' => $this->getCategoryDistribution(),
        ];
    }

    /**
     * Get summary data for helpdesk
     */
    private function getSummary(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $today = Carbon::now()->startOfDay();

        return [
            'this_week' => Ticket::where('created_at', '>=', $startOfWeek)->count(),
            'technicians' => User::role('technician')->where('is_active', true)->count(),
            'unassigned' => Ticket::whereDoesntHave('assignment')->count(),
            'assigned_today' => TicketAssignment::whereDate('assigned_at', $today)->count(),
        ];
    }

    /**
     * Get unassigned tickets
     */
    private function getUnassignedTickets(): array
    {
        return Ticket::whereDoesntHave('assignment')
            ->with([
                'category:id,name',
                'requester:id,name,department_id',
                'requester.department:id,name',
                'status:id,name',
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'category' => $ticket->category ? $ticket->category->name : 'Unknown',
                    'status' => $ticket->status ? $ticket->status->name : 'Unknown',
                    'requester' => [
                        'id' => $ticket->requester->id,
                        'name' => $ticket->requester->name,
                        'department' => $ticket->requester->department ? [
                            'id' => $ticket->requester->department->id,
                            'name' => $ticket->requester->department->name
                        ] : null,
                    ],
                    'created_at' => $ticket->created_at->format('Y-m-d H:i'),
                ];
            })
            ->toArray();
    }
     /**
     * Get ticket trend for last 7 days with incoming and solved counts
     */
    private function getTicketTrend(): array
    {
        $trend = [];
        $today = Carbon::now();
        $closedStatusId = TicketStatus::where('name', 'closed')->value('id') ?? 5;

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateString = $date->toDateString();
            
            // Incoming tickets (created on this date)
            $incoming = Ticket::whereDate('created_at', $dateString)->count();
            
            // Solved tickets (closed on this date)
            $solved = Ticket::whereDate('updated_at', $dateString)
                ->where('status_id', $closedStatusId)
                ->count();

            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'), // Monday, Tuesday, etc.
                'incoming' => $incoming,
                'solved' => $solved,
            ];
        }

        return $trend;
    }
    /**
     * Get category distribution of tickets
     */
    private function getCategoryDistribution(): array
    {
        $startDate = Carbon::now()->subMonth()->startOfDay();
        $total = Ticket::where('created_at', '>=', $startDate)->count();

        return Ticket::select('categories.id', 'categories.name')
            ->selectRaw('COUNT(tickets.id) as count')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->where('tickets.created_at', '>=', $startDate)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($category) use ($total) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'count' => $category->count,
                    'percentage' => $this->calculatePercentage($category->count, $total),
                ];
            })
            ->toArray();
    }
    /**
     * Calculate percentage of total tickets
     */
    private function calculatePercentage($count, $total): float
    {
        if ($total == 0) {
            return 0;
        }
        return round(($count / $total) * 100, 2);
    }
}
