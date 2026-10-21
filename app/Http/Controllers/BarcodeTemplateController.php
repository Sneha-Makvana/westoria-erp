<?php

namespace App\Http\Controllers;

use App\Models\BarcodeTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarcodeTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = BarcodeTemplate::orderBy('name', 'asc')->get();
        return response()->json([
            'status' => true,
            'data' => $templates
        ]);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'width_mm' => 'required|numeric|min:10',
            'height_mm' => 'required|numeric|min:10',
            'printer_type' => 'nullable|string',
            'design_json' => 'nullable|string', // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $template = new BarcodeTemplate();
        $template->name = $request->name;
        $template->width_mm = $request->width_mm;
        $template->height_mm = $request->height_mm;
        $template->printer_type = $request->printer_type;
        $template->design_json = $request->design_json;
        $template->is_default = $request->is_default ?? false;
        
        // Optional: Reset other defaults if this is set to default
        if ($template->is_default) {
            BarcodeTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $template->save();

        return response()->json([
            'status' => true,
            'message' => 'Template saved successfully',
            'data' => $template
        ]);
    }

    /**
     * Display the specified template.
     */
    public function show($id)
    {
        $template = BarcodeTemplate::find($id);
        
        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $template
        ]);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = BarcodeTemplate::find($id);

        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'width_mm' => 'required|numeric|min:10',
            'height_mm' => 'required|numeric|min:10',
            'printer_type' => 'nullable|string',
            'design_json' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $template->name = $request->name;
        $template->width_mm = $request->width_mm;
        $template->height_mm = $request->height_mm;
        $template->printer_type = $request->printer_type;
        $template->design_json = $request->design_json;
        
        if ($request->has('is_default')) {
            $template->is_default = $request->is_default;
            if ($template->is_default) {
                BarcodeTemplate::where('id', '!=', $id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        }

        $template->save();

        return response()->json([
            'status' => true,
            'message' => 'Template updated successfully',
            'data' => $template
        ]);
    }

    /**
     * Rename template only (keeps design and size unchanged).
     */
    public function rename(Request $request, $id)
    {
        $template = BarcodeTemplate::find($id);

        if (! $template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter a valid template name',
                'errors' => $validator->errors(),
            ], 422);
        }

        $template->name = trim((string) $request->name);
        $template->save();

        return response()->json([
            'status' => true,
            'message' => 'Template renamed successfully',
            'data' => $template,
        ]);
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy($id)
    {
        $template = BarcodeTemplate::find($id);

        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'status' => true,
            'message' => 'Template deleted successfully'
        ]);
    }
}
