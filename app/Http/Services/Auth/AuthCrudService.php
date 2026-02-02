<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthCrudService
{
    /**
     * Register new user
     */
    public function registerUser(array $validated): User
    {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'department_id' => $validated['department_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole('requester');

        return $user;
    }

    /**
     * Logout user by deleting current token
     */
    public function logoutUser($token): void
    {
        $token->delete();
    }
}
