<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FinancerController extends Controller
{
    public function getAllFinancer(Request $request)
    {
        $authUser = Auth::guard('api')->user() ?? Auth::user();

        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $branchId = $this->resolveBranchId($authUser, $request->input('selectedSubAdminId'));
        $perPage = (int) $request->get('per_page', 10);
        $search = trim((string) $request->get('search', ''));

        $query = User::with('details')
            ->where('role', 'financer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('pan_number', 'like', "%{$search}%")
                    ->orWhere('gst_number', 'like', "%{$search}%")
                    ->orWhere('account_group', 'like', "%{$search}%");
            });
        }

        $financers = $query->orderByDesc('id')->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $financers->map(function ($financer) {
                return [
                    'id' => $financer->id,
                    'name' => $financer->name,
                    'email' => $financer->email,
                    'phone' => $financer->phone,
                    'profile_image' => $financer->profile_image ?? null,
                    'profile_image_url' => $financer->profile_image_url ?? null,
                    'alternate_phone' => $financer->alternate_phone,
                    'credit_limit' => $financer->credit_limit,
                    'credit_days' => $financer->credit_days,
                    'pan_status' => $financer->pan_status,
                    'pan_number' => $financer->pan_number,
                    'gstin_status' => $financer->gstin_status,
                    'gst_number' => $financer->gst_number,
                    'account_group' => $financer->account_group,
                    'status' => (int) $financer->status,
                    'address' => $financer->details->address ?? '',
                    'address_line2' => $financer->details->address_line2 ?? '',
                    'address_line3' => $financer->details->address_line3 ?? '',
                    'pin_code' => $financer->details->pin_code ?? '',
                    'city' => $financer->details->city ?? '',
                    'state_name' => $financer->state_name ?? '',
                ];
            }),
            'pagination' => [
                'current_page' => $financers->currentPage(),
                'last_page' => $financers->lastPage(),
                'per_page' => $financers->perPage(),
                'total' => $financers->total(),
                'from' => $financers->firstItem(),
                'to' => $financers->lastItem(),
            ],
        ]);
    }

    public function getFinancer(Request $request, $id)
    {
        $authUser = Auth::guard('api')->user() ?? Auth::user();

        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $branchId = $this->resolveBranchId($authUser, $request->input('selectedSubAdminId'));

        $financer = User::with('details')
            ->where('id', $id)
            ->where('role', 'financer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->first();

        if (! $financer) {
            return response()->json([
                'status' => false,
                'message' => 'Financer not found',
            ], 404);
        }

        $data = [
            'id' => $financer->id,
            'name' => $financer->name,
            'email' => $financer->email,
            'phone' => $financer->phone,
            'profile_image' => $financer->profile_image ?? null,
            'profile_image_url' => $financer->profile_image_url ?? null,
            'alternate_phone' => $financer->alternate_phone,
            'credit_limit' => $financer->credit_limit,
            'credit_days' => $financer->credit_days,
            'pan_status' => $financer->pan_status,
            'pan_number' => $financer->pan_number,
            'gstin_status' => $financer->gstin_status,
            'gst_number' => $financer->gst_number,
            'account_group' => $financer->account_group,
            'status' => (int) $financer->status,
            'address' => $financer->details->address ?? '',
            'address_line2' => $financer->details->address_line2 ?? '',
            'address_line3' => $financer->details->address_line3 ?? '',
            'pin_code' => $financer->details->pin_code ?? '',
            'city' => $financer->details->city ?? '',
            'state_name' => $financer->state_name ?? '',
        ];

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function importFinancers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please upload a valid CSV or Excel file.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $authUser = Auth::guard('api')->user() ?? Auth::user();
        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $branchId = $this->resolveBranchId($authUser, $request->input('selectedSubAdminId'));
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $headers = [];
        $headerRowNumber = null;

        foreach ($rows as $rowNumber => $candidateHeaderRow) {
            $candidateHeaders = [];
            foreach ($candidateHeaderRow as $column => $heading) {
                $normalized = $this->normalizeHeader((string) $heading);
                if ($normalized !== '') {
                    $candidateHeaders[$column] = $normalized;
                }
            }

            if (in_array('name', $candidateHeaders, true)) {
                $headers = $candidateHeaders;
                $headerRowNumber = $rowNumber;
                break;
            }
        }

        if ($headerRowNumber === null) {
            return response()->json([
                'status' => false,
                'message' => 'Could not find the header row. Please include Financier Name column.',
            ], 422);
        }

        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $processed = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach (array_slice($rows, $headerRowNumber + 1, null, true) as $index => $row) {
                $lineNumber = $index + 1;
                $mapped = $this->mapRow($row, $headers);

                if ($this->isEmptyRow($mapped)) {
                    continue;
                }

                $name = $this->stringValue($mapped, 'name');
                if ($name === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Financier Name is required.";
                    continue;
                }

                $result = $this->upsertFinancer($mapped, $branchId, $authUser);
                $processed++;
                if ($result['created']) {
                    $created++;
                } elseif ($result['updated']) {
                    $updated++;
                } else {
                    $unchanged++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Financer import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Import failed. Please check the file and try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Financiers imported successfully.',
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'unchanged' => $unchanged,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    public function updateFinancer(Request $request, $id)
    {
        $authUser = Auth::guard('api')->user() ?? Auth::user();
        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pan_number' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric',
            'credit_days' => 'nullable|integer',
            'account_group' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $branchId = $this->resolveBranchId($authUser, $request->input('selectedSubAdminId'));

        $financer = User::where('id', $id)->where('role', 'financer')->where('isDeleted', 0)->where('branch_id', $branchId)->first();
        if (! $financer) {
            return response()->json([
                'status' => false,
                'message' => 'Financer not found',
            ], 404);
        }

        $mapped = [
            'name' => $request->input('name'),
            'phone_1' => $request->input('phone'),
            'phone_2' => $request->input('alternate_phone'),
            'email' => $request->input('email'),
            'pan_number' => $request->input('pan_number'),
            'gst_number' => $request->input('gst_number'),
            'credit_limit' => $request->input('credit_limit'),
            'credit_days' => $request->input('credit_days'),
            'account_group' => $request->input('account_group'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'address_line3' => $request->input('address_line3'),
            'pin_code' => $request->input('pin_code'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'status' => $request->has('status') ? $request->input('status') : null,
        ];

        try {
            DB::beginTransaction();

            $name = $this->stringValue($mapped, 'name');
            $phone1 = $this->normalizePhone($this->stringValue($mapped, 'phone_1'));
            $phone2 = $this->normalizePhone($this->stringValue($mapped, 'phone_2'));
            $email = $this->normalizeEmail($this->stringValue($mapped, 'email'));
            $pan = strtoupper($this->stringValue($mapped, 'pan_number'));
            $gstin = strtoupper($this->stringValue($mapped, 'gst_number'));

            $financer->name = $name;
            $this->assignString($financer, 'state_name', $mapped, 'state');
            $this->assignString($financer, 'pan_status', $mapped, 'pan_status');
            $this->assignString($financer, 'gstin_status', $mapped, 'gstin_status');
            $this->assignString($financer, 'account_group', $mapped, 'account_group');

            if ($phone1 !== '' && $this->canUseValue('phone', $phone1, $financer->id)) {
                $financer->phone = $phone1;
            }

            if ($phone2 !== '') {
                $financer->alternate_phone = $phone2;
            }

            if ($email !== '' && $this->canUseValue('email', $email, $financer->id)) {
                $financer->email = $email;
            }

            if ($pan !== '') {
                $financer->pan_number = $pan;
            }

            if ($gstin !== '') {
                $financer->gst_number = $gstin;
            }

            if ($this->hasValue($mapped, 'credit_limit')) {
                $financer->credit_limit = $this->parseAmount($mapped['credit_limit']);
            }

            if ($this->hasValue($mapped, 'credit_days')) {
                $financer->credit_days = $this->parseInteger($mapped['credit_days']);
            }

            if ($this->hasValue($mapped, 'status')) {
                $financer->status = $this->mapStatus((string) $mapped['status']);
            }

            $wasDirty = $financer->isDirty();
            $financer->save();

            $detail = UserDetail::firstOrNew(['user_id' => $financer->id]);
            $this->assignString($detail, 'address', $mapped, 'address_line1');
            $this->assignString($detail, 'address_line2', $mapped, 'address_line2');
            $this->assignString($detail, 'address_line3', $mapped, 'address_line3');
            $this->assignString($detail, 'pin_code', $mapped, 'pin_code');
            $this->assignString($detail, 'city', $mapped, 'city');

            $detailWasDirty = $detail->isDirty();
            $detail->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Financer update failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to update financer.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Financer updated successfully.',
            'updated' => $wasDirty || $detailWasDirty,
        ]);
    }

    public function deleteFinancer(Request $request, $id)
    {
        $financer = User::find($id);

        if (! $financer || $financer->role !== 'financer') {
            return response()->json(['status' => false, 'message' => 'Financer not found'], 404);
        }

        // Add checks similar to customers if necessary (orders, invoices)

        DB::transaction(function () use ($financer, $id) {
            UserDetail::where('user_id', $id)->update(['isDeleted' => 1]);
            $financer->update(['isDeleted' => 1]);
        });

        return response()->json(['status' => true, 'message' => 'Financer deleted successfully']);
    }

    public function storeFinancer(Request $request)
    {
        $authUser = Auth::guard('api')->user() ?? Auth::user();
        if (! $authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pan_number' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric',
            'credit_days' => 'nullable|integer',
            'account_group' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $branchId = $this->resolveBranchId($authUser, $request->input('selectedSubAdminId'));

        $mapped = [
            'name' => $request->input('name'),
            'phone_1' => $request->input('phone'),
            'phone_2' => $request->input('alternate_phone'),
            'email' => $request->input('email'),
            'pan_number' => $request->input('pan_number'),
            'gst_number' => $request->input('gst_number'),
            'credit_limit' => $request->input('credit_limit'),
            'credit_days' => $request->input('credit_days'),
            'account_group' => $request->input('account_group'),
            'address_line1' => $request->input('address_line1'),
            'address_line2' => $request->input('address_line2'),
            'address_line3' => $request->input('address_line3'),
            'pin_code' => $request->input('pin_code'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'status' => $request->has('status') ? $request->input('status') : null,
        ];

        try {
            DB::beginTransaction();
            $result = $this->upsertFinancer($mapped, $branchId, $authUser);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Financer store failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to create financer.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => $result['created'] ? 'Financer created.' : 'Financer updated or unchanged.',
            'created' => $result['created'],
            'updated' => $result['updated'],
        ]);
    }

    private function upsertFinancer(array $mapped, int $branchId, User $authUser): array
    {
        $name = $this->stringValue($mapped, 'name');
        $phone1 = $this->normalizePhone($this->stringValue($mapped, 'phone_1'));
        $phone2 = $this->normalizePhone($this->stringValue($mapped, 'phone_2'));
        $email = $this->normalizeEmail($this->stringValue($mapped, 'email'));
        $pan = strtoupper($this->stringValue($mapped, 'pan_number'));
        $gstin = strtoupper($this->stringValue($mapped, 'gst_number'));

        $financer = $this->findFinancer($branchId, $mapped, $name, $phone1, $email, $pan, $gstin);
        $created = false;

        if (! $financer) {
            $financer = new User([
                'role' => 'financer',
                'branch_id' => $branchId,
                'created_by' => $authUser->id,
                'status' => 1,
            ]);
            $created = true;
        }

        $financer->name = $name;
        $this->assignString($financer, 'state_name', $mapped, 'state');
        $this->assignString($financer, 'pan_status', $mapped, 'pan_status');
        $this->assignString($financer, 'gstin_status', $mapped, 'gstin_status');
        $this->assignString($financer, 'account_group', $mapped, 'account_group');

        if ($phone1 !== '' && $this->canUseValue('phone', $phone1, $financer->id)) {
            $financer->phone = $phone1;
        }

        if ($phone2 !== '') {
            $financer->alternate_phone = $phone2;
        }

        if ($email !== '' && $this->canUseValue('email', $email, $financer->id)) {
            $financer->email = $email;
        }

        if ($pan !== '') {
            $financer->pan_number = $pan;
        }

        if ($gstin !== '') {
            $financer->gst_number = $gstin;
        }

        if ($this->hasValue($mapped, 'credit_limit')) {
            $financer->credit_limit = $this->parseAmount($mapped['credit_limit']);
        }

        if ($this->hasValue($mapped, 'credit_days')) {
            $financer->credit_days = $this->parseInteger($mapped['credit_days']);
        }

        if ($this->hasValue($mapped, 'status')) {
            $financer->status = $this->mapStatus((string) $mapped['status']);
        }

        $wasDirty = $financer->isDirty();
        $financer->save();

        $detail = UserDetail::firstOrNew(['user_id' => $financer->id]);
        $this->assignString($detail, 'address', $mapped, 'address_line1');
        $this->assignString($detail, 'address_line2', $mapped, 'address_line2');
        $this->assignString($detail, 'address_line3', $mapped, 'address_line3');
        $this->assignString($detail, 'pin_code', $mapped, 'pin_code');
        $this->assignString($detail, 'city', $mapped, 'city');

        $detailWasDirty = $detail->isDirty();
        $detail->save();

        return [
            'created' => $created,
            'updated' => ! $created && ($wasDirty || $detailWasDirty),
        ];
    }

    private function findFinancer(
        int $branchId,
        array $mapped,
        string $name,
        string $phone,
        string $email,
        string $pan,
        string $gstin
    ): ?User
    {
        $baseQuery = User::where('role', 'financer')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0);

        foreach ([
            ['phone', $phone],
            ['email', $email],
            ['pan_number', $pan],
            ['gst_number', $gstin],
            ['name', $name],
        ] as [$column, $value]) {
            if ($value === '') {
                continue;
            }

            $financer = (clone $baseQuery)->where($column, $value)->first();
            if ($financer) {
                return $financer;
            }
        }

        $addressLine1 = $this->stringValue($mapped, 'address_line1');
        $city = $this->stringValue($mapped, 'city');
        $state = $this->stringValue($mapped, 'state');

        if ($name === '' || ($addressLine1 === '' && $city === '' && $state === '')) {
            return null;
        }

        return (clone $baseQuery)
            ->where('name', $name)
            ->where('state_name', $state)
            ->whereHas('details', function ($query) use ($addressLine1, $city) {
                $query->where('address', $addressLine1)
                    ->where('city', $city);
            })
            ->first();
    }

    private function resolveBranchId(User $user, $selectedSubAdminId): int
    {
        if ($user->role === 'staff' && ! empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) ($user->branch_id ?: $user->id);
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        $header = trim((string) $header, '_');

        return match ($header) {
            's_no', 'sn', 'sr_no', 'serial_no' => 'sn',
            'financier_name', 'financer_name', 'finance_name', 'financer', 'financier', 'finance', 'financername', 'financiername', 'name' => 'name',
            'address_line1', 'address_line_1', 'address1', 'address_1' => 'address_line1',
            'address_line2', 'address_line_2', 'address2', 'address_2' => 'address_line2',
            'address_line3', 'address_line_3', 'address3', 'address_3' => 'address_line3',
            'pin_code', 'pincode', 'pin' => 'pin_code',
            'city_town', 'city', 'town' => 'city',
            'state', 'state_name' => 'state',
            'phone_1', 'phone1', 'phone_no_1', 'phone_number_1', 'mobile_1', 'mobile1' => 'phone_1',
            'phone_2', 'phone2', 'phone_no_2', 'phone_number_2', 'mobile_2', 'mobile2' => 'phone_2',
            'email', 'email_id', 'email_address' => 'email',
            'credit_limit' => 'credit_limit',
            'credit_days' => 'credit_days',
            'pan_status' => 'pan_status',
            'pan', 'pan_no', 'pan_number' => 'pan_number',
            'gstin_status', 'gst_status' => 'gstin_status',
            'gstin', 'gst_number', 'gst_no' => 'gst_number',
            'account_group' => 'account_group',
            'status' => 'status',
            default => $header,
        };
    }

    private function mapRow(array $row, array $headers): array
    {
        $mapped = [];

        foreach ($headers as $column => $field) {
            $mapped[$field] = $row[$column] ?? null;
        }

        return $mapped;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function hasValue(array $mapped, string $field): bool
    {
        return array_key_exists($field, $mapped) && trim((string) $mapped[$field]) !== '';
    }

    private function stringValue(array $mapped, string $field): string
    {
        return $this->hasValue($mapped, $field) ? trim((string) $mapped[$field]) : '';
    }

    private function assignString($model, string $modelField, array $mapped, string $importField): void
    {
        if (! $this->hasValue($mapped, $importField)) {
            return;
        }

        $model->{$modelField} = trim((string) $mapped[$importField]);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen((string) $digits) > 10 && str_starts_with((string) $digits, '91')) {
            $digits = substr((string) $digits, -10);
        }

        return (string) $digits;
    }

    private function normalizeEmail(string $email): string
    {
        $email = strtolower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function canUseValue(string $column, string $value, ?int $currentUserId): bool
    {
        return ! User::where($column, $value)
            ->when($currentUserId, fn ($query) => $query->where('id', '!=', $currentUserId))
            ->exists();
    }

    private function parseAmount($value): float
    {
        $value = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return round((float) ($value !== '' ? $value : 0), 2);
    }

    private function parseInteger($value): ?int
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);

        return $value !== '' ? (int) $value : null;
    }

    private function mapStatus(string $status): int
    {
        $status = strtolower(trim($status));

        return in_array($status, ['inactive', 'disabled', 'blocked', 'closed', '0', 'no'], true) ? 0 : 1;
    }
}
