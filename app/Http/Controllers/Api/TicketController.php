<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\AssignTicketRequest;
use App\Http\Requests\Ticket\SolveTicketRequest;
use App\Http\Requests\Ticket\TicketActionRequest;
use App\Http\Services\Ticket\TicketCrudService;
use App\Http\Services\Ticket\TicketQueryService;
use App\Http\Services\Ticket\TicketValidationService;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    private TicketCrudService $crudService;
    private TicketQueryService $queryService;
    private TicketValidationService $validationService;

    public function __construct(
        TicketCrudService $crudService,
        TicketQueryService $queryService,
        TicketValidationService $validationService
    )
    {
        $this->crudService = $crudService;
        $this->queryService = $queryService;
        $this->validationService = $validationService;
    }

    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();

        $ticket = $this->crudService->createTicket($validated, $request->user()->id);
        
        return response()->json([
            'message' => 'Ticket created',
            'ticket' => $ticket,
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Get query parameters for filtering
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $assignedTo = $request->query('assigned_to');
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);

        // Validate page and per_page
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100); // Max 100 items per page

        $result = $this->queryService->listTickets(
            $user,
            $status,
            $categoryId,
            $assignedTo,
            $search,
            $sortBy,
            $sortOrder,
            $page,
            $perPage,
            null,
            null
        );

        return response()->json([
            'message' => 'Tickets retrieved successfully',
            'data' => $result['data'],
            'pagination' => $result['pagination']
        ]);
    }

    /**
     * GET /my-tickets
     * Ambil semua ticket yang dibuat oleh user (requester)
     */
    public function myTickets(Request $request)
    {
        $user = $request->user();

        $tickets = $this->queryService->myTickets($user);

        return response()->json([
            'message' => 'My tickets retrieved successfully',
            'data' => $tickets
        ]);
    }

    /**
     * GET /technician/tickets
     * Ambil semua ticket yang di-assign ke technician yang sedang login
     */
    public function technicianTickets(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('technician')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);

        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);

        $result = $this->queryService->listTickets(
            $user,
            $status,
            $categoryId,
            $user->id,
            $search,
            $sortBy,
            $sortOrder,
            $page,
            $perPage,
            ['closed'],
            null
        );

        return response()->json([
            'message' => 'Technician tickets retrieved successfully',
            'data' => $result['data'],
            'pagination' => $result['pagination']
        ]);
    }

    /**
     * GET /technician/completed-tickets
     * Endpoint khusus untuk teknisi melihat ticket yang sudah resolved/closed
     * permission: role technician
     */
    public function technicianCompletedTickets(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('technician')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $status = $request->query('status'); // Optional: specific 'resolved' or 'closed'
        $categoryId = $request->query('category_id');
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'updated_at'); // Sort by updated_at karena completed ticket
        $sortOrder = $request->query('sort_order', 'desc');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);

        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);

        // Jika status diisi, validasi hanya boleh resolved atau closed
        if ($status && !in_array($status, ['resolved', 'closed'])) {
            return response()->json([
                'message' => 'Status must be either "resolved" or "closed"'
            ], 422);
        }

        // Gunakan includeStatus untuk filter hanya resolved dan closed
        // Jika status diisi (resolved atau closed), gunakan status spesifik
        // Jika tidak diisi, tampilkan semua ticket resolved dan closed
        $includeStatus = $status ? [$status] : ['resolved', 'closed'];

        $result = $this->queryService->listTickets(
            $user,
            null, // status - null karena kita sudah filter dengan includeStatus
            $categoryId,
            $user->id, // assigned_to = current technician
            $search,
            $sortBy,
            $sortOrder,
            $page,
            $perPage,
            null, // excludeStatus
            $includeStatus // includeStatus
        );

        return response()->json([
            'message' => 'Technician completed tickets retrieved successfully',
            'data' => $result['data'],
            'pagination' => $result['pagination']
        ]);
    }

    public function close(Ticket $ticket)
    {
        $this->crudService->closeTicket($ticket, request()->user()->id);

        return response()->json(['message' => 'Ticket closed']);
    }

    public function show(Ticket $ticket)
    {
        $ticket = $this->queryService->getTicketDetail($ticket);

        return response()->json([
            'ticket' => $ticket
        ]);
    }

    /**
     * POST /tickets/{ticket}/assign
     * permission: ticket.assign
     */
    public function assign(AssignTicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        $ticket = $this->crudService->assignTicket($ticket, $validated, $request->user()->id);

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
     * POST /tickets/{ticket}/confirm
     * Technician confirms assigned ticket
     * permission: ticket.change_status
     */
    public function confirm(Request $request, Ticket $ticket)
    {
        $result = $this->crudService->confirmTicket($ticket, $request->user()->id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json([
            'message' => 'Ticket confirmed and now in progress',
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => 'In Progress'
            ]
        ]);
    }

    /**
     * POST /tickets/{ticket}/reject
     * Technician rejects assigned ticket
     * permission: ticket.change_status
     */
    public function reject(TicketActionRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();
        $result = $this->crudService->rejectTicket($ticket, $request->user()->id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json([
            'message' => 'Ticket rejected and returned to Open status',
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => 'Open'
            ]
        ]);
    }

    /**
     * POST /tickets/{ticket}/unresolve
     * Helpdesk unresolves ticket for technician to recheck
     * permission: ticket.assign
     */
    public function unresolve(TicketActionRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();
        $result = $this->crudService->unresolveTicket($ticket, $request->user()->id);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        // TODO: Bisa tambahkan log unresolve reason di ticket_logs atau ticket_comments
        // TicketComment::create([
        //     'ticket_id' => $ticket->id,
        //     'user_id' => $request->user()->id,
        //     'comment' => 'Ticket unresolved: ' . $request->unresolve_reason,
        // ]);

        return response()->json([
            'message' => 'Ticket unresolved and returned to In Progress status',
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'status' => 'In Progress'
            ]
        ]);
    }

    /**
     * POST /tickets/{ticket}/solve
     * permission: ticket.solve
     */
    public function solve(SolveTicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();
        $result = $this->crudService->solveTicket($ticket, $request->user()->id, $validated['solution']);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json([
            'message' => 'Ticket solved successfully'
        ]);
    }

    /**
     * GET /tickets/{ticket}/completion-history
     * Lihat history penyelesaian ticket (siapa teknisi yang menyelesaikannya)
     */
    public function completionHistory(Ticket $ticket)
    {
        $ticket = $this->queryService->completionHistory($ticket);

        return response()->json([
            'message' => 'Ticket completion history retrieved successfully',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'completion_histories' => $ticket->technicianHistories,
            ]
        ]);
    }

    /**
     * GET /tickets/{ticket}/logs
     * Lihat history log ticket
     */
    public function logs(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        $ticket->load(['assignment']);

        $isPrivileged = $user->hasRole('master-admin') || $user->hasRole('helpdesk');
        $isRelatedRequester = $ticket->requester_id === $user->id;
        $isRelatedTechnician = $ticket->assignment && $ticket->assignment->assigned_to === $user->id;

        if (!$isPrivileged && !$isRelatedRequester && !$isRelatedTechnician) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $ticket = $this->queryService->logs($ticket);

        return response()->json([
            'message' => 'Ticket logs retrieved successfully',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'logs' => $ticket->logs,
            ]
        ]);
    }

}
