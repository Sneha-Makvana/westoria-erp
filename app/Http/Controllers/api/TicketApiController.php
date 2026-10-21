<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TicketApiController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        $user = Auth::guard('api')->user();

        if ($user->role === 'staff' && ! empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && ! empty($request->selectedSubAdminId)) {
            return (int) $request->selectedSubAdminId;
        }

        return (int) ($user->branch_id ?? $user->id);
    }

    /**
     * GET /api/tickets
     * Paginated list with search, status, priority filters.
     * Staff: only sees tickets assigned to them.
     * Admin / Sub-admin: sees all tickets in the branch.
     */
    public function index(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $this->resolveBranchId($request);
        $perPage  = (int) $request->input('per_page', 10);
        $search   = trim($request->input('search', ''));

        $query = Ticket::with(['customer:id,name,phone', 'assignedTo:id,name'])
            ->where('branch_id', $branchId)
            ->where('is_deleted', 0);


        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no',   'like', "%{$search}%")
                  ->orWhere('subject',   'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) =>
                      $cq->where('name',  'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderByDesc('id')->paginate($perPage);

        $data = $tickets->map(fn ($t) => [
            'id'            => $t->id,
            'ticket_no'     => $t->ticket_no ?? '-',
            'customer_id'   => $t->customer_id,
            'customer_name' => optional($t->customer)->name ?? '-',
            'subject'       => $t->subject,
            'priority'      => $t->priority,
            'status'        => $t->status,
            'assigned_to'   => optional($t->assignedTo)->name ?? 'Unassigned',
            'attachment'    => $t->attachment ? asset('storage/' . $t->attachment) : null,
            'created_at'    => optional($t->created_at)->format('d-m-Y'),
        ]);

        return response()->json([
            'status' => true,
            'data'   => $data,
            'pagination' => [
                'total'        => $tickets->total(),
                'per_page'     => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page'    => $tickets->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/tickets/{id}/delete
     * Soft-delete a ticket.
     */
    public function destroy(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);

        $ticket = Ticket::where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->find($id);

        if (! $ticket) {
            return response()->json(['status' => false, 'error' => 'Ticket not found.'], 404);
        }

        if (! empty($ticket->attachment)) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->is_deleted = 1;
        $ticket->save();

        return response()->json(['status' => true, 'message' => 'Ticket deleted successfully.']);
    }

    // ─────────────────────────────────────────────────────────────────
    //  Shared: build the filtered query for exports
    // ─────────────────────────────────────────────────────────────────
    private function buildExportQuery(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $this->resolveBranchId($request);
        $search   = trim($request->input('search', ''));

        $query = Ticket::with(['customer:id,name,phone', 'assignedTo:id,name'])
            ->where('branch_id', $branchId)
            ->where('is_deleted', 0);


        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no',   'like', "%{$search}%")
                  ->orWhere('subject',   'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) =>
                      $cq->where('name',  'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        return $query->orderByDesc('id');
    }

    // ─────────────────────────────────────────────────────────────────
    //  GET /api/tickets/export-excel
    // ─────────────────────────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $tickets = $this->buildExportQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tickets');

        // ── Header row ──────────────────────────────────────
        $headers = ['#', 'Ticket No', 'Customer', 'Subject', 'Priority', 'Status', 'Created'];
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Header style
        $headerRange = 'A1:G1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // ── Data rows ────────────────────────────────────────
        foreach ($tickets as $i => $t) {
            $row = $i + 2;
            $rowData = [
                $i + 1,
                $t->ticket_no ?? '-',
                optional($t->customer)->name ?? '-',
                $t->subject,
                ucfirst($t->priority ?? '-'),
                ucwords(str_replace('_', ' ', $t->status ?? '-')),
                // optional($t->assignedTo)->name ?? 'Unassigned',
                optional($t->created_at)->format('d-m-Y'),
            ];
            foreach ($rowData as $col => $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . $row;
                $sheet->setCellValue($cell, $value);
            }

            // Alternating row colour
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                ]);
            }

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FFE2E8F0']]],
            ]);
        }

        // ── Column widths ─────────────────────────────────────
        foreach (['A' => 5, 'B' => 15, 'C' => 22, 'D' => 30, 'E' => 12,
                  'F' => 14, 'G' => 20] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ── Stream directly via StreamedResponse (no temp file, no disk) ──
        $fileName = 'tickets_' . date('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            // Clear any buffered output that could corrupt the binary
            if (ob_get_length()) {
                ob_clean();
            }
            $writer->save('php://output');
        }, 200, [
            'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition'       => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'             => 'max-age=0, no-cache, no-store',
            'Pragma'                    => 'no-cache',
            'Expires'                   => '0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  GET /api/tickets/export-pdf
    // ─────────────────────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $setting  = \App\Models\Setting::where('branch_id', $branchId)->first()
                 ?? \App\Models\Setting::first();

        $tickets = $this->buildExportQuery($request)->get()->map(fn ($t) => [
            'ticket_no'    => $t->ticket_no ?? '-',
            'customer'     => optional($t->customer)->name ?? '-',
            'subject'      => $t->subject,
            'priority'     => ucfirst($t->priority ?? '-'),
            'status'       => ucwords(str_replace('_', ' ', $t->status ?? '-')),
            'assigned_to'  => optional($t->assignedTo)->name ?? 'Unassigned',
            'created_at'   => optional($t->created_at)->format('d-m-Y'),
        ]);

        $fileName = 'tickets_' . date('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('ticket.export_pdf', [
                      'tickets' => $tickets,
                      'setting' => $setting,
                  ])->setPaper('a4', 'landscape');

        // ── Stream directly to browser (works on local & live) ──
        return $pdf->download($fileName);
    }
}
