<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\TicketStatus;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'channel' => 'required|in:web,mobile,email',
        ]);

        $status = TicketStatus::where('name', 'Open')->firstOrFail();

        $ticket = Ticket::create([
            'ticket_number' => Str::uuid(),
            'requester_id' => $request->user()->id,
            'status_id' => $status->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'channel' => $request->channel,
        ]);
        
        return response()->json([
            'message' => 'Ticket created',
            'ticket' => $ticket,
        ], 201);
    }
    
    public function index(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::with(['status', 'category'])
            ->when($user->hasRole('requester'), fn ($q) =>
                $q->where('requester_id', $user->id)
            )
            ->when($user->hasRole('technician'), fn ($q) =>
                $q->whereHas('assignments', fn ($a) =>
                    $a->where('technician_id', $user->id)
                )
            )
            ->latest()
            ->get();

        return response()->json($tickets);
    }
    public function close(Ticket $ticket)
    {
        $ticket->update([
            'status_id' => TicketStatus::where('name', 'Closed')->first()->id,
            'closed_at' => now(),
        ]);

        return response()->json(['message' => 'Ticket closed']);
    }

}
