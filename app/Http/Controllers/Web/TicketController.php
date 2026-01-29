<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;     // Wajib import ini
use Illuminate\Support\Facades\Session;  // Wajib import ini

class TicketController extends Controller
{
    // Helper untuk ambil URL API
    private function getApiUrl() {
        return env('API_BASE_URL', 'http://127.0.0.1:8000');
    }

    // Helper untuk ambil Token dari Session (hasil login)
    private function getToken() {
        return Session::get('api_token');
    }

    // 1. TAMPILKAN LIST TIKET (INDEX)
    public function index()
    {
        $tickets = [];
        try {
            // Ambil data tiket dari API
            $response = Http::withToken($this->getToken())
                            ->get($this->getApiUrl() . '/api/tickets');
            
            if ($response->successful()) {
                $json = $response->json();
                $tickets = $json['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Silent error biar halaman tetap kebuka walau kosong
        }

        return view('tickets.index', compact('tickets'));
    }

    // 2. TAMPILKAN FORM
    public function create()
    {
        // Opsional: Ambil data Kategori dari API untuk dropdown
        $categories = [];
        // $response = Http::get(...) -> logic ambil kategori bisa ditaruh sini
        
        return view('tickets.create', compact('categories')); 
    }

    // 3. SIMPAN DATA (POST KE API)
    public function store(Request $request)
    {
        // Validasi di sisi Web (biar user gak nunggu lama kalau kosong)
        $request->validate([
            'subject' => 'required|max:255',
            'description' => 'required',
        ]);

        try {
            // Ambil data user dari session (karena Auth::id() mungkin null)
            $userData = Session::get('user_data');
            $requesterId = $userData['id'] ?? null;

            // KIRIM KE API
            $response = Http::withToken($this->getToken())
                ->post($this->getApiUrl() . '/api/tickets', [
                    'subject'       => $request->subject,
                    'description'   => $request->description,
                    'category_id'   => $request->category_id ?? 1, // Default kategori
                    'priority_id'   => 1, // Default Low
                    'requester_id'  => $requesterId, // Kirim ID user yang sedang login
                ]);

            if ($response->successful()) {
                return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat!');
            } else {
                return back()->with('error', 'Gagal membuat tiket: ' . ($response->json()['message'] ?? 'API Error'));
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke Server API Gagal.');
        }
    }

    // 4. DETAIL TIKET
    public function show($id)
    {
        $ticket = null;
        try {
            $response = Http::withToken($this->getToken())
                            ->get($this->getApiUrl() . '/api/tickets/' . $id);
            
            if ($response->successful()) {
                $ticket = $response->json()['data'] ?? null;
            }
        } catch (\Exception $e) { }

        // Jika tiket tidak ditemukan di API
        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Tiket tidak ditemukan.');
        }

        return view('tickets.show', compact('ticket'));
    }
}