# Fitur Baru: Ticket Own & Technician History

## 1. Endpoint: GET /my-tickets
**Deskripsi**: Mengambil semua tiket yang dibuat oleh user (requester)

### Request
```
GET /api/my-tickets
Headers: 
  Authorization: Bearer {token}
```

### Response (200 OK)
```json
{
  "message": "My tickets retrieved successfully",
  "data": [
    {
      "id": 1,
      "ticket_number": "TKT-2026-000001",
      "subject": "Internet Connection Problem",
      "description": "My internet is not working",
      "channel": "web",
      "status_id": 1,
      "requester_id": 1,
      "category_id": 2,
      "created_at": "2026-01-29T10:00:00.000000Z",
      "updated_at": "2026-01-29T10:00:00.000000Z",
      "closed_at": null,
      "status": {
        "id": 1,
        "name": "Open"
      },
      "category": {
        "id": 2,
        "name": "Network"
      },
      "requester": {
        "id": 1,
        "name": "Bambang",
        "email": "bambang@arwana.com"
      },
      "assignment": {
        "ticket_id": 1,
        "assigned_to": 5,
        "assigned_by": 2,
        "assigned_at": "2026-01-29T10:30:00.000000Z",
        "notes": "Urgent issue",
        "technician": {
          "id": 5,
          "name": "Budi",
          "email": "budi@arwana.com"
        }
      }
    }
  ]
}
```

---

## 2. Endpoint: GET /tickets/{ticket}/completion-history
**Deskripsi**: Melihat history penyelesaian ticket - siapa teknisi yang menyelesaikannya

### Request
```
GET /api/tickets/1/completion-history
Headers: 
  Authorization: Bearer {token}
```

### Response (200 OK)
```json
{
  "message": "Ticket completion history retrieved successfully",
  "data": {
    "ticket_id": 1,
    "ticket_number": "TKT-2026-000001",
    "completion_histories": [
      {
        "id": 1,
        "ticket_id": 1,
        "technician_id": 5,
        "resolved_at": "2026-01-29T11:45:00.000000Z",
        "solution_text": "Restarted modem and reconnected all cables. Internet is now working properly.",
        "created_at": "2026-01-29T11:45:00.000000Z",
        "updated_at": "2026-01-29T11:45:00.000000Z",
        "technician": {
          "id": 5,
          "name": "Budi",
          "email": "budi@arwana.com"
        }
      }
    ]
  }
}
```

---

## 3. Endpoint: GET /users/{user}/resolved-tickets
**Deskripsi**: Melihat semua ticket yang telah diselesaikan oleh seorang technician

### Request
```
GET /api/users/5/resolved-tickets
Headers: 
  Authorization: Bearer {token}
  Requires permission: user.view (helpdesk or admin)
```

### Response (200 OK)
```json
{
  "message": "Resolved tickets retrieved successfully",
  "data": {
    "technician_id": 5,
    "technician_name": "Budi",
    "total_resolved": 3,
    "resolved_tickets": [
      {
        "id": 1,
        "ticket_id": 1,
        "technician_id": 5,
        "resolved_at": "2026-01-29T11:45:00.000000Z",
        "solution_text": "Restarted modem and reconnected all cables. Internet is now working properly.",
        "created_at": "2026-01-29T11:45:00.000000Z",
        "updated_at": "2026-01-29T11:45:00.000000Z",
        "ticket": {
          "id": 1,
          "ticket_number": "TKT-2026-000001",
          "subject": "Internet Connection Problem",
          "status_id": 5,
          "status": {
            "id": 5,
            "name": "Closed"
          }
        }
      },
      {
        "id": 2,
        "ticket_id": 2,
        "technician_id": 5,
        "resolved_at": "2026-01-29T14:20:00.000000Z",
        "solution_text": "Updated software driver for printer. Now printing works correctly.",
        "created_at": "2026-01-29T14:20:00.000000Z",
        "updated_at": "2026-01-29T14:20:00.000000Z",
        "ticket": {
          "id": 2,
          "ticket_number": "TKT-2026-000002",
          "subject": "Printer Issue",
          "status_id": 5,
          "status": {
            "id": 5,
            "name": "Closed"
          }
        }
      }
    ]
  }
}
```

---

## Bagaimana History Tracking Bekerja?

### Saat Ticket Diselesaikan (`POST /tickets/{ticket}/solve`)

Ketika technician menyelesaikan ticket, sistem secara otomatis:

1. **Menyimpan solution** di table `ticket_solutions` (sudah ada sebelumnya)
2. **Mencatat history** di table `technician_ticket_histories` (baru)
3. **Mengubah status** ticket menjadi "Resolved"

### Request Contoh
```
POST /api/tickets/1/solve
Headers: 
  Authorization: Bearer {technician_token}
  Content-Type: application/json

Body:
{
  "solution": "Restarted modem and reconnected all cables. Internet is now working properly."
}
```

### Response
```json
{
  "message": "Ticket solved successfully"
}
```

Otomatis akan membuat record di `technician_ticket_histories` dengan:
- `ticket_id`: 1
- `technician_id`: 5 (ID technician yang solve)
- `resolved_at`: waktu solve
- `solution_text`: solusi yang diberikan

---

## Database Schema

### Tabel Baru: technician_ticket_histories

```sql
CREATE TABLE technician_ticket_histories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  ticket_id INT NOT NULL,
  technician_id INT NOT NULL,
  resolved_at TIMESTAMP NULL,
  solution_text TEXT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_technician_resolved (technician_id, resolved_at)
);
```

---

## Model Relationships

### Ticket Model
```php
public function technicianHistories()
{
    return $this->hasMany(TechnicianTicketHistory::class);
}
```

### User Model
```php
public function resolvedTicketHistories()
{
    return $this->hasMany(TechnicianTicketHistory::class, 'technician_id');
}
```

### TechnicianTicketHistory Model
```php
public function ticket()
{
    return $this->belongsTo(Ticket::class);
}

public function technician()
{
    return $this->belongsTo(User::class, 'technician_id');
}
```

---

## Catatan Penting

1. **Multiple Resolutions**: Jika ticket di-unresolve dan di-resolve lagi, akan ada record baru di history. Ini memungkinkan tracking jika ticket perlu di-handle ulang.

2. **Timestamp**: 
   - `resolved_at`: Kapan ticket diselesaikan
   - `created_at`, `updated_at`: Otomatis dari Eloquent

3. **Permission**: 
   - `/my-tickets`: Bisa diakses oleh siapa saja (tidak ada permission check khusus)
   - `/tickets/{ticket}/completion-history`: Membutuhkan `ticket.view` permission
   - `/users/{user}/resolved-tickets`: Membutuhkan `user.view` permission (helpdesk+)

4. **Performance**: Index pada `(technician_id, resolved_at)` membantu query yang sering mencari ticket history per technician
