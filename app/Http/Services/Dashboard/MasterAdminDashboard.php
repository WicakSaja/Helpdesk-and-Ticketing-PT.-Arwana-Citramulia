<?php

namespace App\Http\Services\Dashboard;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Department;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterAdminDashboard
{
    /**
     * Get dashboard data for master admin
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),
            'ticket_trend' => $this->getTicketTrend(),
            'role_distribution' => $this->getRoleDistribution(),
            'category_distribution' => $this->getCategoryDistribution(),
            'latest_tickets' => $this->getLatestTickets(),
        ];
    }

    /**
     * Get summary data: users, active technicians, tickets this month, departments
     */
    private function getSummary(): array
    {
        $currentMonth = Carbon::now();

        return [
            'users' => User::count(),
            'technicians' => User::role('technician')->where('is_active', true)->count(),
            'tickets_month' => Ticket::whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->count(),
            'departments' => Department::count(),
        ];
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
     * Get role distribution across all users
     */
    private function getRoleDistribution(): array
    {
        $roles = [
            'master_admin' => 'master-admin',
            'supervisor' => 'supervisor',
            'helpdesk' => 'helpdesk',
            'technician' => 'technician',
            'requester' => 'requester',
        ];

        $distribution = [];
        foreach ($roles as $key => $roleName) {
            $distribution[$key] = User::role($roleName)->count();
        }

        return $distribution;
    }

    /**
     * Get category distribution of tickets
     */
    private function getCategoryDistribution(): array
    {
        return Ticket::select('categories.id', 'categories.name')
            ->selectRaw('COUNT(tickets.id) as count')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'count' => $category->count,
                    'percentage' => $this->calculatePercentage($category->count),
                ];
            })
            ->toArray();
    }

    /**
     * Get latest 5 tickets
     */
    private function getLatestTickets(): array
    {
        return Ticket::with([
            'requester:id,name,email,department_id',
            'requester.department',
            'status:id,name',
            'category:id,name',
            'assignment' => function ($q) {
                $q->with('technician:id,name');
            },
        ])
        ->orderByDesc('created_at')
        ->limit(5)
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'description' => substr($ticket->description, 0, 100) . '...',
                'status' => $ticket->status ? $ticket->status->name : 'Unknown',
                'category' => $ticket->category ? $ticket->category->name : 'Unknown',
                'requester' => $ticket->requester ? (object)[
                    'id' => $ticket->requester->id,
                    'name' => $ticket->requester->name,
                    'email' => $ticket->requester->email,
                    'department' => $ticket->requester->department ? (object)[
                        'id' => $ticket->requester->department->id,
                        'name' => $ticket->requester->department->name,
                    ] : null,
                ] : null,
                'technician' => $ticket->assignment && $ticket->assignment->technician ? (object)[
                    'id' => $ticket->assignment->technician->id,
                    'name' => $ticket->assignment->technician->name,
                ] : null,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
            ];
        })
        ->toArray();
    }

    /**
     * Calculate percentage of total tickets
     */
    private function calculatePercentage($count): float
    {
        $total = Ticket::count();
        if ($total == 0) {
            return 0;
        }
        return round(($count / $total) * 100, 2);
    }
}
