# Copilot Instructions for Ticketing System Arwana

## Project Overview
Laravel 10-based API ticketing system for PT Arwana with role-based permission management (Master Admin, Helpdesk, Technician, Requester). Features structured ticket workflow with status transitions, assignments, and satisfaction surveys.

## Architecture

### Core Components
- **API Routes** (`routes/api.php`): RESTful API using Laravel Sanctum authentication
- **Controllers** (`app/Http/Controllers/Api/`): `AuthController`, `TicketController`, `UserManagementController`
- **Models** (`app/Models/`): Ticket, User, TicketStatus, TicketAssignment, TicketComment, TicketSolution
- **Permissions** (`config/permission.php`): Spatie Laravel Permission for role-based access control

### Key Data Flow
1. Users authenticate via Sanctum tokens (email or phone + password)
2. Tickets are created by requesters, assigned by helpdesk/supervisor to technicians
3. Technicians confirm → progress → resolve; helpdesk/admin can unresolve or close
4. All state changes logged in `TicketLog` model
5. Solutions and comments tracked in `TicketSolution` and `TicketComment` models

## Ticket Workflow & Status States
**Status Progression**: OPEN → ASSIGNED → IN PROGRESS → RESOLVED → CLOSED

- **OPEN**: New ticket from requester. Actions: Assign (by helpdesk/supervisor/admin)
- **ASSIGNED**: Ticket assigned to technician. Actions: Confirm (technician) → IN PROGRESS, or Reject (technician) → OPEN
- **IN PROGRESS**: Technician working. Actions: Resolve (technician) → RESOLVED, or Unresolve (by helpdesk/admin)
- **RESOLVED**: Solution provided. Actions: Close (by admin/supervisor)
- **CLOSED**: Final state. Read-only for audit.

Reference: [TICKET_WORKFLOW.md](TICKET_WORKFLOW.md)

## Development Workflow

### Initial Setup
```bash
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate && php artisan db:seed
```

### Daily Development (Recommended)
**Terminal 1 - Vite hot reload**:
```bash
npm run dev
```
**Terminal 2 - Laravel server**:
```bash
php artisan serve
```
Access: `http://localhost:8000`

### Key Commands
- **Run tests**: `php artisan test`
- **Database reset**: `php artisan migrate:fresh --seed`
- **Generate ticket number**: Format is `TKT-{YYYY}-{ID}` (e.g., `TKT-2026-000001`) - auto-generated in `TicketController::generateTicketNumber()`
- **Recompile assets**: `npm run build` (only if not using `npm run dev`)

## Authorization Patterns

### Permission-Based Middleware
Routes use `middleware('permission:xyz')` with Spatie permissions:
- `ticket.create`: Requester (create ticket)
- `ticket.view`: All authenticated users (filtered by policy)
- `ticket.assign`: Supervisor/Admin/Helpdesk
- `ticket.change_status`: Technician (confirm/reject)
- `ticket.resolve`: Technician
- `ticket.close`: Admin/Supervisor
- `user.view`: Helpdesk+
- `user.view-all`, `user.create`, `user.update`: Master Admin only

### Role Filtering in Controllers
- `TicketController::index()` uses role checks: requesters see only their tickets, technicians see assigned tickets
- Example from code:
```php
->when($user->hasRole('technician'), fn ($q) =>
    $q->whereHas('assignments', fn ($a) => $a->where('technician_id', $user->id))
)
```

## Database & Model Relations

### Key Models & Relationships
- **User**: `hasMany(Ticket)` as requester, `hasMany(TicketAssignment)` as technician, `belongsTo(Department)`
- **Ticket**: `belongsTo(User, requester_id)`, `belongsTo(TicketStatus)`, `belongsTo(Category)`, `hasOne(TicketAssignment)`, `hasMany(TicketComment)`, `hasOne(TicketSolution)`
- **TicketAssignment**: Tracks current technician assignment; `notes` field for assignment metadata
- **TicketStatus**: Static reference data (OPEN, ASSIGNED, IN PROGRESS, RESOLVED, CLOSED)
- **TicketLog**: Audit trail for all status/assignment changes

### Migration Pattern
Migrations are numbered by timestamp (e.g., `2026_01_26_060809_create_tickets_table.php`). Always run `php artisan migrate` for new schemas; order matters due to foreign key dependencies.

## API Response Pattern

All endpoints return JSON with consistent structure:
```json
{
  "message": "Success description",
  "data": {...},
  "token": "sanctum_token" // auth endpoints only
}
```

Errors use standard HTTP status codes (401 unauthorized, 403 forbidden, 422 validation, 404 not found).

## Testing Expectations

- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/` (use `CreatesApplication` trait)
- Run via `php artisan test` or `phpunit`
- Postman collection included (`Ticketing_System_Arwana_Complete_API.postman_collection.json`) for API integration testing

## Project-Specific Conventions

1. **Ticket Number Generation**: Always use `generateTicketNumber()` method (not manual strings)
2. **Authentication**: Use `$request->user()` in controllers (Sanctum authenticated)
3. **Status Lookup**: Query `TicketStatus::where('name', 'StatusName')` (not hardcoded IDs)
4. **Timestamps**: `created_at`, `updated_at` auto-managed by Eloquent; `closed_at` manually set on close
5. **Locale**: Indonesian comments in code (e.g., "Ticket yang dibuat user" = "Tickets created by user")
6. **Technician History Tracking**: When a ticket is resolved via `/solve`, automatically create entry in `TechnicianTicketHistory` to track who resolved it

## Ticket History Features

### New Endpoints
- **GET `/my-tickets`**: Requester views all their created tickets
- **GET `/tickets/{ticket}/completion-history`**: View history of who resolved this ticket
- **GET `/users/{user}/resolved-tickets`**: View all tickets resolved by a specific technician

### History Tracking
- `TechnicianTicketHistory` model records:
  - Which technician resolved the ticket
  - When it was resolved (`resolved_at`)
  - The solution text provided
  - Allows multiple resolutions if ticket is unresolved and re-resolved

## File Organization

- **Controllers**: One per resource (`AuthController`, `TicketController`, `UserManagementController`)
- **Models**: One per database entity, with relations defined as methods
- **Migrations**: Prefixed with timestamp, include foreign keys in dependent tables
- **Seeders**: Located in `database/seeders/` (e.g., `CategorySeeder.php`)
- **Config**: `config/permission.php` manages Spatie roles/permissions setup

