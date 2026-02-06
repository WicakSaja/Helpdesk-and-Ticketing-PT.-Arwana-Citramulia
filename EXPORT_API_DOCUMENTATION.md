# API Export Report Documentation

## Overview
API untuk export report ticket ke dalam format Excel dengan berbagai pilihan format laporan. Endpoint ini hanya dapat diakses oleh **Master Admin** dan **Helpdesk**.

## Endpoint
```
GET /api/export
```

## Authentication
- **Required**: Sanctum Token (Bearer Token)
- **Authorized Roles**: `master-admin`, `helpdesk`

## Query Parameters

### Required Parameters
| Parameter | Type | Description |
|-----------|------|-------------|
| `type` | string | Tipe laporan yang ingin di-export. Nilai: `all-tickets`, `by-status`, `by-technician`, `by-department` |

### Optional Filters
| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `start_date` | date | Tanggal awal filter (format: YYYY-MM-DD) | `2026-01-01` |
| `end_date` | date | Tanggal akhir filter (format: YYYY-MM-DD) | `2026-01-31` |
| `status` | string | Filter berdasarkan status ticket | `OPEN`, `ASSIGNED`, `IN PROGRESS`, `RESOLVED`, `CLOSED` |
| `department_id` | integer | Filter berdasarkan department ID requester | `1`, `2`, `3` |
| `technician_id` | integer | Filter berdasarkan technician/assigned user ID | `5`, `10` |
| `interval` | string | Interval untuk summary (upcoming feature) | `weekly`, `monthly` |

## Response Format

### Success Response (200 OK)
```
Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
Content-Disposition: attachment; filename="Laporan_[type]_[timestamp].xlsx"
```

File Excel akan di-download langsung dengan format sesuai template yang ada di `storage/app/templates/report-templates.xlsx`

**Kolom yang ditampilkan:**
1. Nomor (increment)
2. Nomor Ticket
3. Tanggal Dibuat
4. Nama Requester
5. Keluhan (Subject)
6. Detail (Deskripsi Lengkap)
7. Teknisi yang Diassign
8. Tanggal Selesai (Resolved At)

### Error Response (422 Unprocessable Entity)
```json
{
  "message": "Validation error",
  "errors": {
    "type": ["The type field is required."],
    "end_date": ["The end date must be after or equal to start date."]
  }
}
```

### Error Response (500 Server Error)
```json
{
  "message": "Export failed: [error message]"
}
```

## Report Types

### 1. all-tickets
Menampilkan semua ticket dalam satu daftar dengan urutan terbaru terlebih dahulu.

**Contoh Request:**
```
GET /api/export?type=all-tickets&start_date=2026-01-01&end_date=2026-01-31
```

**Output:**
| Nomor | No. Ticket | Tanggal | Requester | Keluhan | Detail | Teknisi | Selesai |
|-------|-----------|---------|-----------|---------|--------|---------|---------|
| 1 | TKT-2026-000001 | 26-01-2026 10:30 | John Doe | Server Down | Detail... | Jane Smith | 26-01-2026 14:50 |

---

### 2. by-status
Menampilkan ticket di-group berdasarkan statusnya dengan subtotal per status.

**Contoh Request:**
```
GET /api/export?type=by-status&status=RESOLVED&department_id=1
```

**Output:**
| Kelompok | Total |
|----------|-------|
| Status: RESOLVED | 15 |
| 1 | TKT-2026-000001 | 26-01-2026 10:30 | John Doe | ... |
| 2 | TKT-2026-000002 | 26-01-2026 11:00 | Jane Smith | ... |

---

### 3. by-technician
Menampilkan ticket di-group berdasarkan technician yang ditugaskan.

**Contoh Request:**
```
GET /api/export?type=by-technician&start_date=2026-01-01&end_date=2026-01-31
```

**Output:**
| Kelompok | Total |
|----------|-------|
| Teknisi: Ahmad Rahman | 8 |
| 1 | TKT-2026-000001 | ... |
| 2 | TKT-2026-000002 | ... |
| **Teknisi: Siti Nurhaliza** | **12** |
| 3 | TKT-2026-000003 | ... |

---

### 4. by-department
Menampilkan ticket di-group berdasarkan department requester.

**Contoh Request:**
```
GET /api/export?type=by-department&start_date=2026-01-01&end_date=2026-01-31
```

**Output:**
| Kelompok | Total |
|----------|-------|
| Departemen: IT Support | 20 |
| 1 | TKT-2026-000001 | ... |
| **Departemen: Finance** | **15** |
| 2 | TKT-2026-000002 | ... |

---

## Usage Examples

### Example 1: Export semua ticket bulan ini
```bash
curl -X GET "http://localhost:8000/api/export?type=all-tickets&start_date=2026-01-01&end_date=2026-01-31" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" \
  -o report.xlsx
```

### Example 2: Export ticket by technician dengan filtering
```bash
curl -X GET "http://localhost:8000/api/export?type=by-technician&start_date=2026-01-01&end_date=2026-01-31&status=RESOLVED" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o report_by_technician.xlsx
```

### Example 3: Export ticket resolved by specific department
```bash
curl -X GET "http://localhost:8000/api/export?type=by-department&department_id=1&status=RESOLVED" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o report_department_resolved.xlsx
```

### Example 4: Export untuk technician tertentu
```bash
curl -X GET "http://localhost:8000/api/export?type=all-tickets&technician_id=5&start_date=2026-01-01&end_date=2026-01-31" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o report_technician.xlsx
```

## Filename Convention
File yang di-download akan memiliki nama dengan format:
```
Laporan_[type]_[timestamp].xlsx
```

Contoh:
- `Laporan_semua-ticket_2026-01-26_14-30-45.xlsx`
- `Laporan_ticket-by-status_2026-01-26_14-30-45.xlsx`
- `Laporan_ticket-by-technician_2026-01-26_14-30-45.xlsx`
- `Laporan_ticket-by-department_2026-01-26_14-30-45.xlsx`

## Validasi & Error Handling

### Validasi Parameter
- `type` : Required, hanya boleh: `all-tickets`, `by-status`, `by-technician`, `by-department`
- `start_date`, `end_date` : Optional, format YYYY-MM-DD, `end_date` harus >= `start_date`
- `status` : Optional, string nama status atau array ID status
- `department_id`, `technician_id` : Optional, integer ID

### Error Cases
| Status | Pesan | Penyebab |
|--------|-------|---------|
| 422 | `type` field is required | Parameter `type` tidak dikirim |
| 422 | type harus salah satu dari: all-tickets, ... | Parameter `type` tidak valid |
| 422 | end_date harus >= start_date | Date range tidak valid |
| 500 | Export failed: ... | Error saat membaca template atau generate file |
| 401 | Unauthenticated | Token tidak valid atau tidak dikirim |
| 403 | Unauthorized | User tidak memiliki role master-admin atau helpdesk |

## Template File
Template file Excel harus ada di: `storage/app/templates/report-templates.xlsx`

Template harus memiliki minimal:
- Header di row 1-2
- Kolom A-H untuk data
- Formatting yang diinginkan (warna, font, border, dll)

## Development Notes
- Service: `App\Http\Services\ExportService`
- Controller: `App\Http\Controllers\Api\ExportController`
- Route: `GET /api/export`
- Middleware: `auth:sanctum`, `role:master-admin|helpdesk`

## Feature Roadmap
- [ ] Interval weekly/monthly summary (aggregation)
- [ ] Custom column selection
- [ ] Multiple file format support (CSV, PDF)
- [ ] Scheduled export (cron job)
- [ ] Email delivery option
