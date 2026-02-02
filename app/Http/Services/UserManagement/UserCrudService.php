<?php

namespace App\Http\Services\UserManagement;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCrudService
{
    /**
     * Create a new user
     */
    public function createUser(array $validated): User
    {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'department_id' => $validated['department_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->syncRoles($validated['roles']);

        return $user;
    }

    /**
     * Update user data and roles
     */
    public function updateUser(User $user, array $validated): User
    {
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
        if (isset($validated['is_active'])) {
            $user->is_active = $validated['is_active'];
        }
        $user->save();

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return $user;
    }

    /**
     * Update active status
     */
    public function updateStatus(User $user, bool $isActive): User
    {
        $user->is_active = $isActive;
        $user->save();

        return $user;
    }

    /**
     * Reset user password and revoke tokens
     */
    public function resetPassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();

        $user->tokens()->delete();
    }
}
