<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabelFormatController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $formats = $this->getFormatsForBranch($branchId);

        return response()->json([
            'status' => true,
            'formats' => $formats,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'width_mm' => 'nullable|numeric|min:10|max:120',
            'height_mm' => 'nullable|numeric|min:10|max:120',
            'elements' => 'required|array',
        ]);

        $branchId = $this->resolveBranchId($request);
        $settings = $this->getSettingsRow($branchId);
        if (! $settings) {
            return response()->json(['status' => false, 'message' => 'Settings not found for this branch.'], 404);
        }

        $formats = $this->decodeFormats($settings->label_formats ?? null);
        $id = trim((string) $request->input('id', ''));
        if ($id === '') {
            $id = 'fmt_' . Str::lower(Str::random(10));
        }

        $now = now()->toIso8601String();
        $payload = [
            'id' => $id,
            'name' => trim((string) $request->name),
            'width_mm' => (float) ($request->input('width_mm', 35) ?: 35),
            'height_mm' => (float) ($request->input('height_mm', 25) ?: 25),
            'elements' => array_values($request->input('elements', [])),
            'updated_at' => $now,
        ];

        $found = false;
        foreach ($formats as $index => $format) {
            if (($format['id'] ?? '') === $id) {
                $payload['created_at'] = $format['created_at'] ?? $now;
                $formats[$index] = $payload;
                $found = true;
                break;
            }
        }
        if (! $found) {
            $payload['created_at'] = $now;
            $formats[] = $payload;
        }

        DB::table('settings')->where('id', $settings->id)->update([
            'label_formats' => json_encode(array_values($formats)),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Label format saved.',
            'format' => $payload,
            'formats' => $formats,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $branchId = $this->resolveBranchId($request);
        $settings = $this->getSettingsRow($branchId);
        if (! $settings) {
            return response()->json(['status' => false, 'message' => 'Settings not found.'], 404);
        }

        $formats = array_values(array_filter(
            $this->decodeFormats($settings->label_formats ?? null),
            fn ($format) => ($format['id'] ?? '') !== $id
        ));

        DB::table('settings')->where('id', $settings->id)->update([
            'label_formats' => json_encode($formats),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Label format deleted.',
            'formats' => $formats,
        ]);
    }

    public static function findFormatForBranch($branchId, string $formatId): ?array
    {
        $controller = new self();
        $settings = $controller->getSettingsRow($controller->resolveBranchIdFromId($branchId));
        if (! $settings) {
            return null;
        }

        foreach ($controller->decodeFormats($settings->label_formats ?? null) as $format) {
            if (($format['id'] ?? '') === $formatId) {
                return $format;
            }
        }

        return null;
    }

    private function resolveBranchId(Request $request): int
    {
        $user = Auth::guard('api')->user();
        $requested = $request->input('branch_id') ?? $request->input('sub_branch_id');

        if ($requested) {
            return (int) $requested;
        }

        return $this->resolveBranchIdFromUser($user);
    }

    private function resolveBranchIdFromId($branchId): int
    {
        return is_numeric($branchId) ? (int) $branchId : 0;
    }

    private function resolveBranchIdFromUser($user): int
    {
        if (! $user) {
            return (int) (DB::table('settings')->value('branch_id') ?? 0);
        }

        return match (strtolower((string) $user->role)) {
            'staff' => (int) ($user->branch_id ?? $user->id),
            default => (int) $user->id,
        };
    }

    private function getSettingsRow(int $branchId): ?object
    {
        if ($branchId > 0) {
            $row = DB::table('settings')->where('branch_id', $branchId)->first();
            if ($row) {
                return $row;
            }
        }

        return DB::table('settings')->orderBy('id')->first();
    }

    private function getFormatsForBranch(int $branchId): array
    {
        $settings = $this->getSettingsRow($branchId);

        return $settings ? $this->decodeFormats($settings->label_formats ?? null) : [];
    }

    private function decodeFormats($raw): array
    {
        if (is_array($raw)) {
            return array_values($raw);
        }
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? array_values($decoded) : [];
        }

        return [];
    }
}
