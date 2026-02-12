<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Services\Auth\AuthCrudService;
use App\Http\Services\Auth\AuthQueryService;
use App\Http\Services\Auth\AuthValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    private AuthCrudService $crudService;
    private AuthQueryService $queryService;
    private AuthValidationService $validationService;

    public function __construct(
        AuthCrudService $crudService,
        AuthQueryService $queryService,
        AuthValidationService $validationService
    )
    {
        $this->crudService = $crudService;
        $this->queryService = $queryService;
        $this->validationService = $validationService;
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = $this->crudService->registerUser($validated);

        // Kirim email verifikasi hanya jika fitur enabled
        if (config('emailverification.enabled')) {
            $user->sendEmailVerificationNotification();
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $userData = $this->queryService->getCurrentUser($user);

        return response()->json([
            'message' => 'Register success',
            'user' => $userData['user'],
            'roles' => $userData['roles'],
            'permissions' => $userData['permissions'],
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $loginField = $this->queryService->getLoginField($validated['login']);

        $user = $this->queryService->authenticateUser($loginField, $validated['login'], $validated['password']);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$this->queryService->isUserActive($user)) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact administrator.'
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = $this->queryService->getCurrentUser($user);

        return response()->json([
            'message' => 'Login success',
            'user' => $userData['user'],
            'roles' => $userData['roles'],
            'permissions' => $userData['permissions'],
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $this->crudService->logoutUser($request->user()->currentAccessToken());

        return response()->json([
            'message' => 'Logout success'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json($this->queryService->getCurrentUser($user));
    }

    public function validateToken(Request $request)
    {
        $user = $request->user();
        $userData = $this->queryService->getCurrentUser($user);

        return response()->json([
            'message' => 'Token is valid',
            'valid' => true,
            'user' => $userData['user'],
            'roles' => $userData['roles'],
            'permissions' => $userData['permissions'],
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai',
                'errors' => [
                    'old_password' => ['Password lama tidak sesuai'],
                ],
            ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        $currentToken = $request->user()->currentAccessToken();
        if ($currentToken instanceof PersonalAccessToken) {
            $user->tokens()->where('id', '!=', $currentToken->id)->delete();
        }

        return response()->json([
            'message' => 'Password berhasil diperbarui',
        ]);
    }
}