<?php

namespace App\Http\Controllers\Web; // Namespace Wajib

use App\Http\Controllers\Controller; // Induk Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    private function getApiConfig()
    {
        return [
            'token' => Session::get('api_token'),
            'url'   => env('API_BASE_URL', 'http://127.0.0.1:8000/api')
        ];
    }

    // INDEX
    public function index()
    {
        $api = $this->getApiConfig();
        try {
            $response = Http::withToken($api['token'])->get($api['url'] . '/tickets');
            $tickets = $response->successful() ? $response->json()['data'] : [];
            return view('tickets.index', compact('tickets'));
        } catch (\Exception $e) {
            return view('tickets.index', ['tickets' => []])->with('error', 'Gagal ambil data.');
        }
    }

    // CREATE
    public function create()
    {
        return view('tickets.create');
    }

    // STORE
    public function store(Request $request)
    {
        $api = $this->getApiConfig();
        
        $request->validate([
            'subject' => 'required',
            'description' => 'required'
        ]);

        try {
            $response = Http::withToken($api['token'])->post($api['url'] . '/tickets', [
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => 'Low'
            ]);

            if ($response->successful()) {
                return redirect()->route('tickets.index')->with('success', 'Tiket Terkirim!');
            }
            
            return back()->with('error', 'Gagal kirim tiket.')->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Error Server.')->withInput();
        }
    }

    // SHOW
    public function show($id)
    {
        $api = $this->getApiConfig();
        try {
            $response = Http::withToken($api['token'])->get($api['url'] . '/tickets/' . $id);
            if ($response->successful()) {
                $ticket = $response->json()['data'];
                return view('tickets.show', compact('ticket'));
            }
            return redirect()->route('tickets.index')->with('error', 'Tiket tidak ditemukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal load tiket.');
        }
    }
}