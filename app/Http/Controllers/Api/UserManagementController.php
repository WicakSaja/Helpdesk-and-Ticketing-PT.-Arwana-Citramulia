<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Models\TechnicianTicketHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Construct - Middleware untuk authentication
     * Role checking dilakukan di method level atau Form Request
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Check if user can view users (master-admin atau helpdesk)
     */
    private function checkCanViewUsers($user)
    {
        if (!$user->hasPermissionTo('user.view')) {
            abort(403, 'You do not have permission to view users');
        }
    }

    /**
     * Check if user is master-admin
     */
    private function checkMasterAdminRole($user)
    {
        if (!$user->hasRole('master-admin')) {
            abort(403, 'Only master-admin can access this feature');
        }
    }

   
    /**
     * Get semua users (master-admin + helpdesk)
     */
    public function index(Request $request)
    {
        $this->checkCanViewUsers($request->user());
        
        $query = User::query();

        // Filter by role jika ada
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by department jika ada
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Search by name atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->with('department')
            ->paginate($request->per_page ?? 15);

        // Add roles ke setiap user
        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department_id' => $user->department_id,
                'department' => $user->department,
                'roles' => $user->getRoleNames(),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }

    /**
     * Get detail user (master-admin + helpdesk)
     */
    public function show(User $user)
    {
        $this->checkCanViewUsers(auth()->user());
        
        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department_id' => $user->department_id,
                'department' => $user->department,
                'roles' => $user->getRoleNames(),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Create user baru
     */
    public function store(StoreUserRequest $request)
    {
        $this->checkMasterAdminRole($request->user());
        
        $validated = $request->validated();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'department_id' => $validated['department_id'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            // Assign roles
            $user->syncRoles($validated['roles']);

            return response()->json([
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department_id' => $user->department_id,
                    'department' => $user->department,
                    'roles' => $user->getRoleNames(),
                    'created_at' => $user->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->checkMasterAdminRole($request->user());
        
        // Tidak bisa edit user dengan role master-admin
        if ($user->hasRole('master-admin')) {
            return response()->json([
                'message' => 'Cannot edit master-admin user',
            ], 403);
        }

        $validated = $request->validated();

        try {
            // Update user data
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $user->phone = $validated['phone'];
            }
            if (isset($validated['department_id'])) {
                $user->department_id = $validated['department_id'];
            }
            $user->save();

            // Update roles jika ada
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            return response()->json([
                'message' => 'User updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department_id' => $user->department_id,
                    'department' => $user->department,
                    'roles' => $user->getRoleNames(),
                    'updated_at' => $user->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password user
     */
    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        $this->checkMasterAdminRole($request->user());
        
        // Tidak bisa reset password user dengan role master-admin
        if ($user->hasRole('master-admin')) {
            return response()->json([
                'message' => 'Cannot reset master-admin password',
            ], 403);
        }

        $validated = $request->validated();

        try {
            $user->password = Hash::make($validated['password']);
            $user->save();

            // Optional: Revoke semua token user jika ada
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Password reset successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'message' => 'All active sessions have been logged out',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available roles
     */
    public function getAvailableRoles()
    {
        $this->checkMasterAdminRole(auth()->user());
        
        $roles = ['helpdesk', 'technician', 'supervisor'];

        return response()->json([
            'message' => 'Available roles retrieved successfully',
            'data' => $roles,
        ]);
    }

    /**
     * Get users by specific role (master-admin + helpdesk)
     * GET /api/users/by-role/{roleName}
     */
    public function getUsersByRole(Request $request, $roleName)
    {
        $this->checkCanViewUsers($request->user());

        // Validate role exists
        $validRoles = ['helpdesk', 'technician', 'supervisor', 'requester', 'manager'];
        
        if (!in_array($roleName, $validRoles)) {
            return response()->json([
                'message' => 'Invalid role',
                'valid_roles' => $validRoles,
            ], 400);
        }

        // Get users with specified role
        $users = User::role($roleName)
            ->with(['department:id,name', 'roles:id,name'])
            ->select('id', 'name', 'email', 'phone', 'department_id', 'created_at')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department' => $user->department ? [
                        'id' => $user->department->id,
                        'name' => $user->department->name,
                    ] : null,
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => $user->created_at,
                ];
            });

        return response()->json([
            'message' => 'Users retrieved successfully',
            'role' => $roleName,
            'count' => $users->count(),
            'data' => $users,
        ]);
    }

    /**
     * Get all roles with user count (master-admin + helpdesk)
     * GET /api/users/roles-summary
     */
    public function getRolesSummary(Request $request)
    {
        $this->checkCanViewUsers($request->user());

        $roles = ['helpdesk', 'technician', 'supervisor', 'requester', 'manager', 'master-admin'];
        
        $summary = collect($roles)->map(function ($roleName) {
            $count = User::role($roleName)->count();
            
            return [
                'role' => $roleName,
                'user_count' => $count,
            ];
        });

        return response()->json([
            'message' => 'Roles summary retrieved successfully',
            'data' => $summary,
        ]);
    }

    /**
     * GET /users/{user}/resolved-tickets
     * Lihat semua ticket yang telah diselesaikan oleh technician
     */
    public function resolvedTickets(Request $request, User $user)
    {
        $this->checkCanViewUsers($request->user());

        // Load resolved ticket histories dengan relasi ticket
        $resolvedTickets = $user->resolvedTicketHistories()
            ->with(['ticket:id,ticket_number,subject,status_id', 'ticket.status:id,name'])
            ->latest('resolved_at')
            ->get();

        return response()->json([
            'message' => 'Resolved tickets retrieved successfully',
            'data' => [
                'technician_id' => $user->id,
                'technician_name' => $user->name,
                'total_resolved' => $resolvedTickets->count(),
                'resolved_tickets' => $resolvedTickets,
            ]
        ]);
    }
}
