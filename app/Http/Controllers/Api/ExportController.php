<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\ExportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export tickets to Excel
     * 
     * Query Parameters:
     * - type: all-tickets|by-status|by-technician|by-department (required)
     * - interval: weekly|monthly (optional)
     * - start_date: YYYY-MM-DD (optional)
     * - end_date: YYYY-MM-DD (optional)
     * - status: status name or array of status ids (optional)
     * - department_id: department id (optional)
     * - technician_id: technician user id (optional)
     */
    public function export(Request $request)
    {
        try {
            // Validate required parameter
            $validated = $request->validate([
                'type' => 'required|in:all-tickets,by-status,by-technician,by-department',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|string',
                'department_id' => 'nullable|integer',
                'technician_id' => 'nullable|integer',
                'interval' => 'nullable|in:weekly,monthly',
            ]);

            // Prepare filters
            $filters = [
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status'),
                'department_id' => $request->get('department_id'),
                'technician_id' => $request->get('technician_id'),
                'interval' => $request->get('interval'),
            ];

            // Generate spreadsheet
            $spreadsheet = $this->exportService->export($validated['type'], $filters);

            // Prepare filename dengan timestamp
            $filename = $this->generateFilename($validated['type']);

            // Return as download
            return $this->downloadSpreadsheet($spreadsheet, $filename);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate filename untuk export
     */
    private function generateFilename($type)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $typeLabel = match ($type) {
            'all-tickets' => 'semua-ticket',
            'by-status' => 'ticket-by-status',
            'by-technician' => 'ticket-by-technician',
            'by-department' => 'ticket-by-department',
            default => 'report',
        };

        return "Laporan_{$typeLabel}_{$timestamp}.xlsx";
    }

    /**
     * Download spreadsheet sebagai response
     */
    private function downloadSpreadsheet($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
