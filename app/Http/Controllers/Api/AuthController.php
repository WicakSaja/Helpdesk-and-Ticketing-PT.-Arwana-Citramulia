<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Services\Auth\AuthCrudService;
use App\Http\Services\Auth\AuthQueryService;
use App\Http\Services\Auth\AuthValidationService;
use Illuminate\Http\Request;

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

        // Load relationships for complete user data
        $user->load('roles.permissions');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register success',
            'user' => $user,
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
            auth()->logout();
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact administrator.'
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Load relationships for complete user data
        $user->load('roles.permissions');

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
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
}