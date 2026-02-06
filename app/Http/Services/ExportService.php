<?php

namespace App\Http\Services;

use App\Models\Ticket;
use App\Models\TicketStatus;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class ExportService
{
    protected $templatePath;
    protected $spreadsheet;
    protected $worksheet;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/report-templates.xlsx');
    }

    /**
     * Generate export based on type
     * Supported types: all-tickets, by-status, by-technician, by-department
     */
    public function export($type, $filters = [])
    {
        // Load template
        $this->spreadsheet = IOFactory::load($this->templatePath);
        $this->worksheet = $this->spreadsheet->getActiveSheet();

        // Get data based on type
        $data = $this->getData($type, $filters);

        // Populate data to template
        $this->populateData($data);

        return $this->spreadsheet;
    }

    /**
     * Get data based on report type
     */
    private function getData($type, $filters)
    {
        $query = $this->buildQuery($filters);

        return match ($type) {
            'all-tickets' => $this->formatAllTickets($query->get()),
            'by-status' => $this->formatByStatus($query->get()),
            'by-technician' => $this->formatByTechnician($query->get()),
            'by-department' => $this->formatByDepartment($query->get()),
            default => collect(),
        };
    }

    /**
     * Build base query with filters
     */
    private function buildQuery($filters)
    {
        $query = Ticket::with([
            'requester',
            'status',
            'category',
            'assignment.technician',
            'solution'
        ]);

        // Date range filter (start_date dan end_date)
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        // Status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status_id', $filters['status']);
            } else {
                $status = TicketStatus::where('name', $filters['status'])->first();
                if ($status) {
                    $query->where('status_id', $status->id);
                }
            }
        }

        // Department filter (by requester's department)
        if (!empty($filters['department_id'])) {
            $query->whereHas('requester', fn ($q) => 
                $q->where('department_id', $filters['department_id'])
            );
        }

        // Technician filter
        if (!empty($filters['technician_id'])) {
            $query->whereHas('assignment', fn ($q) => 
                $q->where('assigned_to', $filters['technician_id'])
            );
        }

        // Interval filter untuk weekly/monthly summary
        if (!empty($filters['interval'])) {
            // This will be handled in formatting methods
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Format data untuk semua tickets
     */
    private function formatAllTickets($tickets)
    {
        return $tickets->map(function ($ticket, $index) {
            return [
                'nomor' => $index + 1,
                'ticket_number' => $ticket->ticket_number,
                'created_at' => $ticket->created_at->format('d-m-Y H:i'),
                'requester_name' => $ticket->requester?->name ?? '-',
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'technician_name' => $ticket->assignment?->technician?->name ?? '-',
                'resolved_at' => $ticket->solution?->solved_at 
                    ? Carbon::parse($ticket->solution->solved_at)->format('d-m-Y H:i')
                    : '-',
                'status' => $ticket->status?->name ?? '-',
            ];
        });
    }

    /**
     * Format data grouped by status
     */
    private function formatByStatus($tickets)
    {
        $grouped = $tickets->groupBy(function ($ticket) {
            return $ticket->status?->name ?? 'Unknown';
        });

        $result = collect();
        foreach ($grouped as $status => $items) {
            $result->push([
                'group' => "Status: $status",
                'count' => count($items),
                'items' => $items,
            ]);
        }

        return $result;
    }

    /**
     * Format data grouped by technician
     */
    private function formatByTechnician($tickets)
    {
        $grouped = $tickets->groupBy(function ($ticket) {
            return $ticket->assignment?->technician?->name ?? 'Unassigned';
        });

        $result = collect();
        foreach ($grouped as $technician => $items) {
            $result->push([
                'group' => "Teknisi: $technician",
                'count' => count($items),
                'items' => $items,
            ]);
        }

        return $result;
    }

    /**
     * Format data grouped by department
     */
    private function formatByDepartment($tickets)
    {
        $grouped = $tickets->groupBy(function ($ticket) {
            return $ticket->requester?->department?->name ?? 'Unknown';
        });

        $result = collect();
        foreach ($grouped as $department => $items) {
            $result->push([
                'group' => "Departemen: $department",
                'count' => count($items),
                'items' => $items,
            ]);
        }

        return $result;
    }

    /**
     * Populate data ke dalam template
     */
    private function populateData($data)
    {
        // Mulai dari row 3 (row 1-2 untuk header template)
        $row = 6;

        foreach ($data as $item) {
            if (isset($item['group'])) {
                // Format grouped data
                $groupItems = $item['items'];
                
                $this->worksheet->setCellValue("A$row", $item['group']);
                $this->worksheet->setCellValue("B$row", "Total: " . $item['count']);
                $this->applyBorderToRow($row, 'B'); // Border untuk header group
                $row++;

                foreach ($groupItems as $index => $ticket) {
                    // Data masih dalam bentuk model object
                    $this->worksheet->setCellValue("A$row", $index + 1);
                    $this->worksheet->setCellValue("B$row", $ticket->ticket_number ?? '-');
                    $this->worksheet->setCellValue("C$row", $ticket->created_at?->format('d-m-Y H:i') ?? '-');
                    $this->worksheet->setCellValue("D$row", $ticket->requester?->name ?? '-');
                    $this->worksheet->setCellValue("E$row", $ticket->subject ?? '-');
                    $this->worksheet->setCellValue("F$row", $ticket->description ?? '-');
                    $this->worksheet->setCellValue("G$row", $ticket->assignment?->technician?->name ?? '-');
                    $this->worksheet->setCellValue("H$row", $ticket->solution?->solved_at 
                        ? Carbon::parse($ticket->solution->solved_at)->format('d-m-Y H:i')
                        : '-');
                    $this->applyBorderToRow($row, 'H'); // Border untuk data row
                    $row++;
                }
                $row++; // Spacing antar group
            } else {
                // Format regular data (all-tickets) - data sudah flattened
                $this->worksheet->setCellValue("A$row", $item['nomor']);
                $this->worksheet->setCellValue("B$row", $item['ticket_number']);
                $this->worksheet->setCellValue("C$row", $item['created_at']);
                $this->worksheet->setCellValue("D$row", $item['requester_name']);
                $this->worksheet->setCellValue("E$row", $item['subject']);
                $this->worksheet->setCellValue("F$row", $item['description']);
                $this->worksheet->setCellValue("G$row", $item['technician_name']);
                $this->worksheet->setCellValue("H$row", $item['resolved_at']);
                $this->applyBorderToRow($row, 'H'); // Border untuk data row
                $row++;
            }
        }
    }

    /**
     * Apply border style to a row
     */
    private function applyBorderToRow($row, $lastColumn = 'H')
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $this->worksheet->getStyle("A$row:$lastColumn$row")->applyFromArray($styleArray);
    }
}
