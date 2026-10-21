<?php

namespace App\Http\Controllers;

use App\Exports\GstValidationExport;
use App\Services\GstWorkbookValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class GstWorkbookValidationController extends Controller
{
    public function __construct(private readonly GstWorkbookValidationService $service)
    {
    }

    public function create()
    {
        return view('gst.validation.upload');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'gst_workbook' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ]);

        try {
            $result = $this->service->validateUploadedWorkbook($request->file('gst_workbook'));

            return redirect()->route('gst.validation.result', $result['id']);
        } catch (Throwable $e) {
            Log::error('GST validation upload failed', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['gst_workbook' => $e->getMessage() ?: 'Unable to validate this GST workbook.']);
        }
    }

    public function show(string $id)
    {
        try {
            $result = $this->service->storedResult($id);
        } catch (Throwable $e) {
            abort(404, $e->getMessage());
        }

        return view('gst.validation.result', compact('result'));
    }

    public function download(string $id): BinaryFileResponse
    {
        try {
            $result = $this->service->storedResult($id);
        } catch (Throwable $e) {
            abort(404, $e->getMessage());
        }

        $fileName = 'GST_Validation_Corrected_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new GstValidationExport($result), $fileName);
    }
}
