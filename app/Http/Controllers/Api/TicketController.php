<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketSolution;
use App\Models\TicketAssignment;

class TicketController extends Controller
{
    /**
     * Generate ticket number dengan format TKT-YYYY-XXXXXX
     */
    private function generateTicketNumber($ticketId)
    {
        $year = date('Y');
        $number = str_pad($ticketId, 6, '0', STR_PAD_LEFT);
        return "TKT-{$year}-{$number}";
    }

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
            'ticket_number' => '',  // Temporary value, akan di-update setelah insert
            'requester_id' => $request->user()->id,
            'status_id' => $status->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'channel' => $request->channel,
        ]);

        // Update ticket_number dengan format yang benar
        $ticket->update([
            'ticket_number' => $this->generateTicketNumber($ticket->id)
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

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'requester:id,name,email',
            'category:id,name',
            'status:id,name',
            'assignment.technician:id,name,email',
            'assignment.assigner:id,name,email',
            'solution',
        ]);

        return response()->json([
            'ticket' => $ticket
        ]);
    }

    /**
     * POST /tickets/{ticket}/assign
     * permission: ticket.assign
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $ticket) {

            TicketAssignment::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'assigned_to' => $request->assigned_to,
                    'assigned_by' => $request->user()->id,
                    'assigned_at' => now(),
                    'notes' => $request->notes,
                ]
            );

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'In Progress')->firstOrFail()->id
            ]);
        });

        // Reload ticket dengan assignment
        $ticket->load('assignment.technician', 'status');

        return response()->json([
            'message' => 'Ticket assigned successfully',
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->status->name,
                'assigned_to' => $ticket->assignment ? [
                    'id' => $ticket->assignment->assigned_to,
                    'name' => $ticket->assignment->technician->name ?? 'Unknown'
                ] : null
            ]
        ]);
    }

    /**
     * POST /tickets/{ticket}/solve
     * permission: ticket.solve
     */
    public function solve(Request $request, Ticket $ticket)
    {
        $request->validate([
            'solution' => 'required|string|min:10',
        ]);

        // Load relations
        $ticket->load(['assignment', 'status']);

        // Ticket harus sudah di-assign
        if (! $ticket->assignment) {
            return response()->json([
                'message' => 'Ticket has not been assigned'
            ], 422);
        }

        // Hanya technician yang di-assign boleh solve
        if ($ticket->assignment->assigned_to !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not assigned to this ticket'
            ], 403);
        }

        // Tidak boleh solve ticket yang sudah closed
        if ($ticket->status->name === 'Closed') {
            return response()->json([
                'message' => 'Ticket already closed'
            ], 422);
        }

        DB::transaction(function () use ($request, $ticket) {

            TicketSolution::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'solution_text' => $request->solution,
                    'solved_by' => $request->user()->id,
                    'solved_at' => now(),
                ]
            );

            $ticket->update([
                'status_id' => TicketStatus::where('name', 'Resolved')->firstOrFail()->id,
            ]);
        });

        return response()->json([
            'message' => 'Ticket solved successfully'
        ]);
    }

}
