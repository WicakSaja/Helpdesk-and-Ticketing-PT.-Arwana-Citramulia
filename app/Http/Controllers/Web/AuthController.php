<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // =========================================================================
    // 1. LOGIN
    // =========================================================================

    public function showLoginForm()
    {
        if (session()->has('user_data')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi Input
        $request->validate(['email' => 'required', 'password' => 'required']);

        try {
            $apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000');
            
            // Tembak API Login
            $response = Http::post($apiUrl . '/api/login', [
                'login' => $request->email, 
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Simpan Session Web
                Session::put('api_token', $data['token'] ?? null);
                Session::put('user_data', $data['user'] ?? []);
                Session::put('user_roles', $data['roles'] ?? []);

                // Return JSON agar JS di frontend bisa memproses redirect
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Login Berhasil!',
                    'redirect_url' => route('dashboard')
                ]);
            }

            // Jika Gagal Login
            return response()->json([
                'status' => 'error', 
                'message' => $response->json()['message'] ?? 'Email atau Password salah.'
            ], 401);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Koneksi API Gagal.'], 500);
        }
    }

    // =========================================================================
    // 2. REGISTER
    // =========================================================================

    public function showRegisterForm()
    {
        if (session()->has('user_data')) {
            return redirect()->route('dashboard');
        }

        $departments = [];

        try {
            $apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000');
            
            // Ambil data Departemen untuk Dropdown
            $response = Http::get($apiUrl . '/api/departments');

            if ($response->successful()) {
                $json = $response->json();
                $departments = $json['data'] ?? $json; 
            }

        } catch (\Exception $e) {
            // Biarkan kosong jika API mati
        }

        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        // Validasi input di sisi Web
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required|confirmed',
            'department_id' => 'required' 
        ]);

        try {
            $apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000');

            // Tembak API Register
            $response = Http::post($apiUrl . '/api/register', [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'department_id' => $request->department_id 
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Auto Login setelah Register (Simpan Session)
                Session::put('api_token', $data['token']);
                Session::put('user_data', $data['user']);
                Session::put('user_roles', ['Requester']); // Default role requester

                // Return JSON sukses
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Registrasi Berhasil!',
                    'redirect_url' => route('dashboard')
                ]);
            }

            // Error dari API (misal: Email sudah ada)
            return response()->json([
                'status' => 'error', 
                'message' => $response->json()['message'] ?? 'Registrasi gagal.',
                'errors' => $response->json()['errors'] ?? []
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Server: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 3. LOGOUT
    // =========================================================================

    public function logout()
    {
        // Opsional: Tembak API Logout jika diperlukan
        try {
            $apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000');
            $token = Session::get('api_token');
            
            if ($token) {
                Http::withToken($token)->post($apiUrl . '/api/logout');
            }
        } catch (\Exception $e) {
            // Abaikan jika API mati, yang penting web session hancur
        }

        Session::flush(); // Hapus semua session
        return redirect()->route('login')->with('success', 'Berhasil Logout');
    }
}