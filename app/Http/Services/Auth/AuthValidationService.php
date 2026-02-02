<?php

namespace App\Http\Services\Auth;

use App\Models\User;

class AuthValidationService
{
    /**
     * Validate if email exists
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Validate if phone exists
     */
    public function phoneExists(string $phone): bool
    {
        return User::where('phone', $phone)->exists();
    }

    /**
     * Validate credentials
     */
    public function validateCredentials(string $loginField, string $login, string $password): bool
    {
        return auth()->attempt([
            $loginField => $login,
            'password' => $password,
        ]);
    }
}
