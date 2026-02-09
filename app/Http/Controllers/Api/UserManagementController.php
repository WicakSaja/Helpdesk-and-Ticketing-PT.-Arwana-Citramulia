<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\StoreUserRequest;
use App\Http\Requests\UserManagement\UpdateUserRequest;
use App\Http\Requests\UserManagement\ResetPasswordRequest;
use App\Http\Services\UserManagement\UserCrudService;
use App\Http\Services\UserManagement\UserQueryService;
use App\Http\Services\UserManagement\UserRoleService;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    private UserCrudService $crudService;
    private UserQueryService $queryService;
    private UserRoleService $roleService;

    /**
     * Construct - Middleware untuk authentication
     * Role checking dilakukan di method level atau Form Request
     */
    public function __construct(
        UserCrudService $crudService,
        UserQueryService $queryService,
        UserRoleService $roleService
    )
    {
        $this->middleware('auth:sanctum');
        $this->crudService = $crudService;
        $this->queryService = $queryService;
        $this->roleService = $roleService;
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

        $users = $this->queryService->listUsers($request);

        $users->getCollection()->transform(function ($user) {
            return $this->queryService->mapUserDetail($user);
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
            'data' => $this->queryService->mapUserDetail($user),
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
            $user = $this->crudService->createUser($validated);

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
                    'is_active' => $user->is_active,
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
            $user = $this->crudService->updateUser($user, $validated);

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
                    'is_active' => $user->is_active,
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
     * Update user status (active/nonactive)
     */
    public function updateStatus(Request $request, User $user)
    {
        $this->checkMasterAdminRole($request->user());

        if ($user->hasRole('master-admin')) {
            return response()->json([
                'message' => 'Cannot update master-admin status',
            ], 403);
        }

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user = $this->crudService->updateStatus($user, $validated['is_active']);

        return response()->json([
            'message' => 'User status updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department_id' => $user->department_id,
                'department' => $user->department,
                'roles' => $user->getRoleNames(),
                'is_active' => $user->is_active,
                'updated_at' => $user->updated_at,
            ],
        ]);
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
            $this->crudService->resetPassword($user, $validated['password']);

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

        $roles = $this->roleService->getAvailableRoles();

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
        $validRoles = $this->roleService->getValidRoles();
        
        if (!in_array($roleName, $validRoles)) {
            return response()->json([
                'message' => 'Invalid role',
                'valid_roles' => $validRoles,
            ], 400);
        }

        // Get users with specified role
        $users = $this->queryService->getUsersByRole($roleName);

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

        $roles = $this->roleService->getSummaryRoles();
        $summary = $this->queryService->getRolesSummary($roles);

        return response()->json([
            'message' => 'Roles summary retrieved successfully',
            'data' => $summary,
        ]);
    }

    /**
     * GET /users/{user}/resolved-tickets
     * Lihat semua ticket yang telah diselesaikan oleh technician
     * Accessible by: master-admin, helpdesk, or technician viewing their own data
     */
    public function resolvedTickets(Request $request, User $user)
    {
        $currentUser = $request->user();
        
        // Allow if master-admin or helpdesk
        if ($currentUser->hasPermissionTo('user.view')) {
            // Has permission to view all users
        } 
        // Allow if technician viewing their own data
        elseif ($currentUser->hasRole('technician') && $currentUser->id === $user->id) {
            // Allow technician to view their own resolved tickets
        } 
        else {
            abort(403, 'You do not have permission to view this user\'s resolved tickets');
        }

        // Load resolved ticket histories dengan relasi ticket
        $resolvedTickets = $this->queryService->getResolvedTickets($user);

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

    /**
     * GET /users/{user}/assigned-tickets
     * Lihat semua ticket yang sedang/ pernah ditangani oleh technician
     * Accessible by: master-admin, helpdesk, or technician viewing their own data
     */
    public function assignedTickets(Request $request, User $user)
    {
        $currentUser = $request->user();

        if ($currentUser->hasPermissionTo('user.view')) {
            // Has permission to view all users
        } elseif ($currentUser->hasRole('technician') && $currentUser->id === $user->id) {
            // Allow technician to view their own assigned tickets
        } else {
            abort(403, 'You do not have permission to view this user\'s assigned tickets');
        }

        if (!$user->hasRole('technician')) {
            return response()->json([
                'message' => 'User is not a technician',
            ], 404);
        }

        $assignedTickets = $this->queryService->getAssignedTickets($user);

        return response()->json([
            'message' => 'Assigned tickets retrieved successfully',
            'data' => [
                'technician_id' => $user->id,
                'technician_name' => $user->name,
                'total_assigned' => $assignedTickets->count(),
                'assigned_tickets' => $assignedTickets,
            ]
        ]);
    }

    /**
     * Get active technicians only
     * GET /api/technicians/active
     * Permission: master-admin, helpdesk
     */
    public function getActiveTechnicians(Request $request)
    {
        $this->checkCanViewUsers($request->user());

        $technicians = User::role('technician')
            ->where('is_active', true)
            ->select('id', 'name', 'email', 'phone', 'department_id', 'is_active', 'created_at')
            ->with('department:id,name')
            ->orderBy('name')
            ->get()
            ->map(function ($technician) {
                return [
                    'id' => $technician->id,
                    'name' => $technician->name,
                    'email' => $technician->email,
                    'phone' => $technician->phone,
                    'department' => $technician->department ? [
                        'id' => $technician->department->id,
                        'name' => $technician->department->name,
                    ] : null,
                    'is_active' => $technician->is_active,
                    'created_at' => $technician->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'message' => 'Active technicians retrieved successfully',
            'data' => $technicians,
            'count' => $technicians->count(),
        ]);
    }
}
