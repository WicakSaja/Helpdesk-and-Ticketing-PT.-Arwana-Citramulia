<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthQueryService
{
    /**
     * Authenticate user and return result
     */
    public function authenticateUser(string $loginField, string $login, string $password): ?User
    {
        $user = User::where($loginField, $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Check if user is active
     */
    public function isUserActive(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Get current user with roles and permissions
     */
    public function getCurrentUser(User $user): array
    {
        // Load department relation
        $user->load('department');
        
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'department_id' => $user->department_id,
                'department_name' => $user->department?->name ?? null,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'email_verification_required' => (bool) config('emailverification.enabled'),
        ];
    }

    /**
     * Determine login field (email or phone)
     */
    public function getLoginField(string $login): string
    {
        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }
}
