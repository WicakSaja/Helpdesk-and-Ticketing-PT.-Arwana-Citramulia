# Ticket Workflow Documentation

## Overview
Sistem ticketing PT Arwana menggunakan workflow yang terstruktur dengan status-status yang jelas dan action yang spesifik untuk setiap role.

## Workflow Diagram

```
┌─────────────┐
│   REQUESTER │
│  Create     │
│   Ticket    │
└──────┬──────┘
       │
       v
   ┌────────┐
   │  OPEN  │◄──────────────────┐
   └───┬────┘                   │
       │                        │
       │ Helpdesk Assign        │ Technician Reject
       v                        │
  ┌──────────┐                  │
  │ ASSIGNED │──────────────────┘
  └────┬─────┘
       │
       │ Technician Confirm
       v
┌──────────────┐
│ IN PROGRESS  │◄──────────────┐
└──────┬───────┘                │
       │                        │
       │ Technician Resolve     │ Helpdesk Unresolve
       v                        │
  ┌──────────┐                  │
  │ RESOLVED │──────────────────┘
  └────┬─────┘
       │
       │ Helpdesk Close
       v
   ┌────────┐
   │ CLOSED │ (FINAL)
   └────────┘
```

## Status Flow

### 1. OPEN
**Deskripsi**: Ticket baru dibuat oleh requester

**Siapa yang bisa lihat**: 
- Requester (pembuat ticket)
- Helpdesk
- Supervisor
- Admin

**Action yang tersedia**:
- **Assign** (Helpdesk/Supervisor/Admin) → Status berubah ke ASSIGNED

---

### 2. ASSIGNED
**Deskripsi**: Ticket sudah di-assign ke technician tertentu

**Siapa yang bisa lihat**:
- Requester (pembuat ticket)
- Technician yang di-assign
- Helpdesk
- Supervisor
- Admin

**Action yang tersedia**:
- **Confirm** (Technician yang di-assign) → Status berubah ke IN PROGRESS
- **Reject** (Technician yang di-assign) → Status kembali ke OPEN, assignment dihapus

---

### 3. IN PROGRESS
**Deskripsi**: Technician sedang mengerjakan ticket

**Siapa yang bisa lihat**:
- Requester (pembuat ticket)
- Technician yang di-assign
- Helpdesk
- Supervisor
- Admin

**Action yang tersedia**:
- **Resolve** (Technician yang di-assign) → Status berubah ke RESOLVED

---

### 4. RESOLVED
**Deskripsi**: Technician sudah menyelesaikan ticket dan memberikan solusi

**Siapa yang bisa lihat**:
- Requester (pembuat ticket)
- Technician yang di-assign
- Helpdesk
- Supervisor
- Admin

**Action yang tersedia**:
- **Close** (Helpdesk/Supervisor/Admin) → Status berubah ke CLOSED (final)
- **Unresolve** (Helpdesk/Supervisor/Admin) → Status kembali ke IN PROGRESS (untuk dicek ulang oleh technician)

---

### 5. CLOSED
**Deskripsi**: Ticket sudah selesai dan ditutup

**Siapa yang bisa lihat**:
- Requester (pembuat ticket)
- Technician yang di-assign
- Helpdesk
- Supervisor
- Admin

**Action yang tersedia**: **TIDAK ADA** (Status final, tidak bisa diubah lagi)

---

## API Endpoints

### 1. Create Ticket
**Endpoint**: `POST /api/tickets`  
**Role**: Requester  
**Permission**: `ticket.create`  
**Body**:
```json
{
  "subject": "Komputer tidak bisa menyala",
  "description": "Komputer di ruang admin lantai 2 tidak bisa menyala sejak pagi ini",
  "category_id": 1,
  "channel": "web"
}
```
**Status**: Open

---

### 2. Assign Ticket
**Endpoint**: `POST /api/tickets/{ticket}/assign`  
**Role**: Helpdesk, Supervisor, Admin  
**Permission**: `ticket.assign`  
**Body**:
```json
{
  "assigned_to": 5,
  "notes": "Assigned ke technician untuk penanganan segera"
}
```
**Status Flow**: Open → Assigned

---

### 3. Confirm Ticket
**Endpoint**: `POST /api/tickets/{ticket}/confirm`  
**Role**: Technician (yang di-assign)  
**Permission**: `ticket.change_status`  
**Body**: `{}` (empty)  
**Status Flow**: Assigned → In Progress

**Validation**:
- Hanya technician yang di-assign yang bisa confirm
- Ticket harus berstatus Assigned

---

### 4. Reject Ticket
**Endpoint**: `POST /api/tickets/{ticket}/reject`  
**Role**: Technician (yang di-assign)  
**Permission**: `ticket.change_status`  
**Body**:
```json
{
  "rejection_reason": "Saya tidak memiliki tools yang diperlukan untuk menangani issue hardware ini"
}
```
**Status Flow**: Assigned → Open (assignment dihapus)

**Validation**:
- Hanya technician yang di-assign yang bisa reject
- Ticket harus berstatus Assigned
- Rejection reason minimal 10 karakter

---

### 5. Resolve Ticket
**Endpoint**: `POST /api/tickets/{ticket}/solve`  
**Role**: Technician (yang di-assign)  
**Permission**: `ticket.resolve`  
**Body**:
```json
{
  "solution": "Sudah dicek kabel power ternyata tidak terpasang dengan baik. Sudah dipasang kembali dengan benar dan komputer sudah bisa menyala normal."
}
```
**Status Flow**: In Progress → Resolved

**Validation**:
- Hanya technician yang di-assign yang bisa resolve
- Ticket harus berstatus In Progress
- Solution minimal 10 karakter

---

### 6. Unresolve Ticket
**Endpoint**: `POST /api/tickets/{ticket}/unresolve`  
**Role**: Helpdesk, Supervisor, Admin  
**Permission**: `ticket.assign`  
**Body**:
```json
{
  "unresolve_reason": "Masalah masih terjadi setelah dicek ulang. Komputer masih tidak bisa menyala. Mohon dicek kembali"
}
```
**Status Flow**: Resolved → In Progress

**Validation**:
- Ticket harus berstatus Resolved
- Ticket harus punya technician assignment
- Unresolve reason minimal 10 karakter

---

### 7. Close Ticket
**Endpoint**: `POST /api/tickets/{ticket}/close`  
**Role**: Helpdesk, Supervisor, Admin  
**Permission**: `ticket.close`  
**Body**:
```json
{
  "closing_notes": "Ticket sudah diselesaikan dengan baik. User sudah konfirmasi komputer berjalan normal."
}
```
**Status Flow**: Resolved → Closed (FINAL)

**Validation**:
- Closing notes bersifat optional

---

## Role Permissions

### Requester
- `ticket.create` - Membuat ticket baru
- `ticket.view` - Melihat ticket
- `ticket.view.own` - Hanya bisa lihat ticket sendiri
- `ticket.comment` - Menambahkan comment

### Technician
- `ticket.view` - Melihat ticket
- `ticket.view.own` - Hanya bisa lihat ticket yang di-assign
- `ticket.change_status` - Confirm/Reject ticket
- `ticket.resolve` - Resolve ticket
- `ticket.comment` - Menambahkan comment

### Helpdesk
- `user.view` - Melihat daftar user (untuk assign)
- `ticket.view` - Melihat ticket
- `ticket.view.all` - Bisa lihat semua ticket
- `ticket.assign` - Assign ticket ke technician
- `ticket.change_status` - Ubah status ticket
- `ticket.comment` - Menambahkan comment
- `ticket.close` - Close ticket
- `ticket.assign` (juga untuk unresolve)

### Supervisor
- `ticket.view` - Melihat ticket
- `ticket.view.all` - Bisa lihat semua ticket
- `ticket.assign` - Assign ticket
- `ticket.escalate` - Escalate ticket
- `ticket.change_status` - Ubah status
- `ticket.view.dashboard` - Akses dashboard

### Master Admin
- Semua permission (full access)

### Manager
- `ticket.view` - Melihat ticket
- `ticket.view.all` - Bisa lihat semua ticket
- `ticket.view.dashboard` - Akses dashboard
- `ticket.view.report` - Akses report

---

## Business Rules

1. **Ticket harus di-assign sebelum bisa dikerjakan**
   - Technician tidak bisa langsung mengerjakan ticket yang belum di-assign

2. **Technician bisa menolak assignment**
   - Jika reject, ticket kembali ke status Open dan bisa di-assign ke technician lain
   - Rejection reason wajib diisi (minimal 10 karakter)

3. **Ticket harus di-confirm sebelum bisa di-resolve**
   - Technician harus confirm ticket terlebih dahulu (Assigned → In Progress)
   - Baru setelah In Progress, technician bisa resolve

4. **Helpdesk bisa unresolve ticket jika masalah belum selesai**
   - Ticket kembali ke status In Progress
   - Technician yang sama yang harus mengecek ulang

5. **Status Closed adalah final**
   - Setelah closed, tidak ada action yang bisa dilakukan
   - Ticket tidak bisa dibuka kembali

6. **Assignment tetap ada sampai ticket closed**
   - Assignment hanya dihapus jika technician reject
   - Setelah confirm, assignment permanent sampai ticket closed

---

## Testing Workflow

### Happy Path
1. Requester create ticket → Status: Open
2. Helpdesk assign ke Technician A → Status: Assigned
3. Technician A confirm → Status: In Progress
4. Technician A resolve dengan solution → Status: Resolved
5. Helpdesk close → Status: Closed ✓

### Rejection Path
1. Requester create ticket → Status: Open
2. Helpdesk assign ke Technician A → Status: Assigned
3. Technician A reject → Status: Open (assignment dihapus)
4. Helpdesk assign ke Technician B → Status: Assigned
5. Technician B confirm → Status: In Progress
6. Technician B resolve → Status: Resolved
7. Helpdesk close → Status: Closed ✓

### Unresolve Path
1. Requester create ticket → Status: Open
2. Helpdesk assign ke Technician A → Status: Assigned
3. Technician A confirm → Status: In Progress
4. Technician A resolve → Status: Resolved
5. Helpdesk unresolve (masalah belum selesai) → Status: In Progress
6. Technician A resolve lagi → Status: Resolved
7. Helpdesk close → Status: Closed ✓

---

## Notes

- Semua action mencatat timestamp secara otomatis
- Assignment mencatat `assigned_at`, `assigned_by`, `assigned_to`, dan `notes`
- Solution mencatat `solved_at`, `solved_by`, dan `solution_text`
- Ticket mencatat `closed_at` saat di-close
- Untuk tracking rejection dan unresolve reason, bisa ditambahkan ke ticket_comments atau ticket_logs di future enhancement
