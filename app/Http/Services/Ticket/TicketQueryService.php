<?php

namespace App\Http\Services\Ticket;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class TicketQueryService
{
    /**
     * Get tickets for index endpoint dengan support filter
     * Query parameters:
     * - status: filter berdasarkan status (open, assigned, in progress, resolved, closed)
     * - category_id: filter berdasarkan category
     * - assigned_to: filter berdasarkan technician (ID)
     * - search: search berdasarkan subject atau ticket_number
     * - sort_by: sort berdasarkan field (created_at, ticket_number, subject) - default: created_at
     * - sort_order: sort order (asc, desc) - default: desc
     * - page: halaman (default: 1)
    * - per_page: jumlah item per halaman (default: 15)
    * - start_date: filter tanggal awal (format: YYYY-MM-DD)
    * - end_date: filter tanggal akhir (format: YYYY-MM-DD)
     * - exclude_status: array status untuk dikecualikan
     * - include_status: array status untuk diinclude (hanya ticket dengan status ini yang akan muncul)
     */
    public function listTickets(User $user, ?string $status = null, ?int $categoryId = null, ?int $assignedTo = null, ?string $search = null, string $sortBy = 'created_at', string $sortOrder = 'desc', int $page = 1, int $perPage = 15, ?array $excludeStatus = null, ?array $includeStatus = null, ?string $startDate = null, ?string $endDate = null)
    {
        $query = Ticket::with([
            'status',
            'category',
            'requester.department',
            'assignment.technician',
            'solution'
        ]);

        // Role-based filtering
        if ($user->hasRole('requester')) {
            $query->where('requester_id', $user->id);
        } elseif ($user->hasRole('technician')) {
            $query->whereHas('assignment', fn ($a) =>
                $a->where('assigned_to', $user->id)
            );
        }

        // Filter by status
        if ($status) {
            $query->whereHas('status', fn ($q) =>
                $q->where('name', strtolower($status))
            );
        }

        // Include only specific statuses
        if ($includeStatus && count($includeStatus) > 0) {
            $normalized = array_map(fn ($s) => strtolower($s), $includeStatus);
            $query->whereHas('status', fn ($q) =>
                $q->whereIn('name', $normalized)
            );
        }

        // Exclude statuses
        if ($excludeStatus && count($excludeStatus) > 0) {
            $normalized = array_map(fn ($s) => strtolower($s), $excludeStatus);
            $query->whereHas('status', fn ($q) =>
                $q->whereNotIn('name', $normalized)
            );
        }

        // Filter by category
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter by assigned technician
        if ($assignedTo) {
            $query->whereHas('assignment', fn ($q) =>
                $q->where('assigned_to', $assignedTo)
            );
        }

        // Search by subject or ticket_number
        if ($search) {
            $query->where(fn ($q) =>
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
            );
        }

        // Filter by created_at date range
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Sorting
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $total = $query->count();
        $tickets = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();

        return [
            'data' => $tickets,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => min($page * $perPage, $total),
            ]
        ];
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
     * Ticket detail with relations (complete info including logs, comments, history)
     */
    public function getTicketDetail(Ticket $ticket): Ticket
    {
        $ticket->load([
            'requester:id,name,email,phone,department_id',
            'requester.department:id,name',
            'category:id,name',
            'status:id,name',
            'assignment.technician:id,name,email',
            'assignment.assigner:id,name,email',
            'solution:id,ticket_id,solved_by,solution_text,solved_at',
            'solution.solver:id,name,email',
            'comments:id,ticket_id,user_id,comment,created_at',
            'comments.user:id,name,email',
            'logs:id,ticket_id,user_id,action,description,created_at',
            'logs.user:id,name,email',
            'technicianHistories:id,ticket_id,technician_id,resolved_at,solution_text',
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
     * Ticket logs history
     */
    public function logs(Ticket $ticket): Ticket
    {
        $ticket->load([
            'logs' => function ($q) {
                $q->with('user:id,name,email')
                  ->orderByDesc('created_at');
            }
        ]);

        return $ticket;
    }
}
