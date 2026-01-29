<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle login - fetch dari API
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Fetch dari API endpoint
            $response = Http::post(env('API_BASE_URL') . '/api/login', [
                'login' => $validated['login'],
                'password' => $validated['password'],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Simpan token dan user info ke session
                session([
                    'auth_token' => $data['token'],
                    'user' => $data['user'],
                    'roles' => $data['roles'] ?? [],
                    'permissions' => $data['permissions'] ?? [],
                ]);

                // Redirect based on role
                $role = $data['roles'][0] ?? 'requester';
                
                if (in_array('master-admin', $data['roles'])) {
                    return redirect()->route('dashboard.admin');
                } elseif (in_array('helpdesk', $data['roles']) || in_array('supervisor', $data['roles'])) {
                    return redirect()->route('dashboard.helpdesk');
                } elseif (in_array('technician', $data['roles'])) {
                    return redirect()->route('dashboard.technician');
                } else {
                    return redirect()->route('dashboard.requester');
                }
            } else {
                return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Connection error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Handle register - fetch dari API
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|min:8|confirmed',
            'department_id' => 'nullable|integer',
        ]);

        try {
            // Fetch dari API endpoint
            $response = Http::post(env('API_BASE_URL') . '/api/register', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'password_confirmation' => $validated['password'],
                'department_id' => $validated['department_id'] ?? null,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Simpan token dan user info ke session
                session([
                    'auth_token' => $data['token'],
                    'user' => $data['user'],
                    'roles' => ['requester'], // Default role untuk user baru
                ]);

                return redirect()->route('dashboard.requester')->with('success', 'Registration successful!');
            } else {
                $errors = $response->json()['errors'] ?? ['message' => 'Registration failed'];
                return back()->withErrors($errors)->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Connection error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            // Optional: notify API tentang logout
            $token = session('auth_token');
            if ($token) {
                Http::withToken($token)->post(env('API_BASE_URL') . '/api/logout');
            }
        } catch (\Exception $e) {
            // Silent fail - just logout from web
        }

        // Clear session
        session()->flush();
        
        return redirect()->route('home')->with('success', 'Logged out successfully');
    }

    /**
     * Get current user info - fetch dari API
     */
    public function getCurrentUser(Request $request)
    {
        $token = session('auth_token');

        if (!$token) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        try {
            $response = Http::withToken($token)
                ->get(env('API_BASE_URL') . '/api/me');

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                session()->flush();
                return response()->json(['error' => 'Token expired'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection error'], 500);
        }
    }
}
