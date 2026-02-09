<?php

namespace App\Http\Services\UserManagement;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserQueryService
{
    /**
     * Get users with filters and pagination
     */
    public function listUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        return $query->with('department')
            ->paginate($request->per_page ?? 15);
    }

    /**
     * Map user detail response
     */
    public function mapUserDetail(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'department_id' => $user->department_id,
            'department' => $user->department,
            'roles' => $user->getRoleNames(),
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    /**
     * Get users by role with role-specific data
     */
    public function getUsersByRole(string $roleName): Collection
    {
        $query = User::role($roleName)
            ->with(['department:id,name', 'roles:id,name'])
            ->select('id', 'name', 'email', 'phone', 'department_id', 'is_active', 'created_at');

        if ($roleName === 'technician') {
            $query->withCount([
                'assignedTickets as assigned_total',
                'assignedTickets as assigned_closed' => function ($q) {
                    $q->whereHas('ticket.status', function ($statusQuery) {
                        $statusQuery->where('name', 'closed');
                    });
                },
            ]);
        }

        return $query->get()
            ->map(function ($user) use ($roleName) {
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department' => $user->department ? [
                        'id' => $user->department->id,
                        'name' => $user->department->name,
                    ] : null,
                    'roles' => $user->roles->pluck('name'),
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ];

                if ($roleName === 'technician') {
                    $total = (int) ($user->assigned_total ?? 0);
                    $completed = (int) ($user->assigned_closed ?? 0);
                    $inProgress = max($total - $completed, 0);

                    $userData['ticket_statistics'] = [
                        'in_progress' => $inProgress,
                        'completed' => $completed,
                        'total' => $total,
                    ];
                }

                return $userData;
            });
    }

    /**
     * Roles summary with user counts
     */
    public function getRolesSummary(array $roles): Collection
    {
        return collect($roles)->map(function ($roleName) {
            $count = User::role($roleName)->count();

            return [
                'role' => $roleName,
                'user_count' => $count,
            ];
        });
    }

    /**
     * Resolved tickets history by technician
     */
    public function getResolvedTickets(User $user)
    {
        return $user->resolvedTicketHistories()
            ->with(['ticket:id,ticket_number,subject,status_id', 'ticket.status:id,name'])
            ->latest('resolved_at')
            ->get();
    }

    /**
     * Assigned tickets detail for technician
     */
    public function getAssignedTickets(User $user): Collection
    {
        return $user->assignedTickets()
            ->with([
                'ticket' => function ($tq) {
                    $tq->select('id', 'ticket_number', 'subject', 'description', 'status_id', 'category_id', 'requester_id', 'created_at');
                    $tq->with([
                        'status:id,name',
                        'category:id,name',
                        'requester:id,name,email,phone',
                    ]);
                },
            ])
            ->get()
            ->map(function ($assignment) {
                return [
                    'assignment_id' => $assignment->id,
                    'assigned_at' => $assignment->assigned_at,
                    'notes' => $assignment->notes,
                    'ticket' => [
                        'id' => $assignment->ticket->id,
                        'ticket_number' => $assignment->ticket->ticket_number,
                        'subject' => $assignment->ticket->subject,
                        'description' => $assignment->ticket->description,
                        'status' => [
                            'id' => $assignment->ticket->status->id ?? null,
                            'name' => $assignment->ticket->status->name ?? null,
                        ],
                        'category' => [
                            'id' => $assignment->ticket->category->id ?? null,
                            'name' => $assignment->ticket->category->name ?? null,
                        ],
                        'requester' => [
                            'id' => $assignment->ticket->requester->id ?? null,
                            'name' => $assignment->ticket->requester->name ?? null,
                            'email' => $assignment->ticket->requester->email ?? null,
                            'phone' => $assignment->ticket->requester->phone ?? null,
                        ],
                        'created_at' => $assignment->ticket->created_at,
                    ],
                ];
            });
    }
}
