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
}
