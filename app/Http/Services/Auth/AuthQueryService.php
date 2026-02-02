<?php

namespace App\Http\Services\Auth;

use App\Models\User;

class AuthQueryService
{
    /**
     * Authenticate user and return result
     */
    public function authenticateUser(string $loginField, string $login, string $password): ?User
    {
        if (!auth()->attempt([
            $loginField => $login,
            'password' => $password,
        ])) {
            return null;
        }

        return User::where($loginField, $login)->first();
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
        return [
            'user' => $user->only([
                'id',
                'name',
                'email',
                'phone',
                'department_id',
                'is_active',
                'created_at',
                'updated_at',
            ]),
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
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
