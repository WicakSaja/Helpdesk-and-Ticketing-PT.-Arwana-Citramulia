<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    // Helper: Ambil URL API
    private function getApiUrl() {
        return env('API_BASE_URL', 'http://127.0.0.1:8000');
    }

    // Helper: Ambil Token Admin
    private function getToken() {
        return Session::get('api_token');
    }

    // 1. READ (Ambil Data)
    public function index()
    {
        $departments = [];
        try {
            $response = Http::withToken($this->getToken())->get($this->getApiUrl() . '/api/departments');
            if ($response->successful()) {
                $json = $response->json();
                $departments = $json['data'] ?? $json; 
            }
        } catch (\Exception $e) { }

        return view('superadmin.departments.index', compact('departments'));
    }

    // 2. CREATE & UPDATE
    public function store(Request $request)
    {
        $url = $this->getApiUrl() . '/api/departments';
        $method = 'post';
        if ($request->id) {
            $url .= '/' . $request->id;
            $method = 'put';
        }
        try {
            $response = Http::withToken($this->getToken())->$method($url, [
                'name' => $request->name
            ]);
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal koneksi ke Server API.'], 500);
        }
    }

    // 3. DELETE
    public function destroy($id)
    {
        try {
            // DELETE ke /api/departments/{id}
            $response = Http::withToken($this->getToken())->delete($this->getApiUrl() . '/api/departments/' . $id);
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}