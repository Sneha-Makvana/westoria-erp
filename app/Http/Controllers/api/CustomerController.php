<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\StaffDepartmentScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerController extends Controller
{
    /* ---------------------------------------------------------
       Helper: Resolve Branch ID
    --------------------------------------------------------- */
    private function resolveBranchId($authUser, Request $request)
    {
        if ($authUser->role === 'staff' && $authUser->branch_id) {
            return $authUser->branch_id;
        }

        if ($authUser->role === 'admin' && ! empty($request->selectedSubAdminId)) {
            return (int) $request->selectedSubAdminId;
        }

        return $authUser->id;
    }

    /* ---------------------------------------------------------
       Get All Customers
    --------------------------------------------------------- */
    // public function getAllCustomer(Request $request)
    // {
    //     $user        = Auth::guard('api')->user();
    //     $branchId    = $this->resolveBranchId($user, $request);

    //     $query = User::select('id', 'name', 'email', 'phone', 'profile_image', 'gst_number', 'pan_number')
    //         ->where('role', 'customer')
    //         ->where('isDeleted', 0)
    //         ->where('branch_id', $branchId)
    //         ->with('details:country,user_id,city');

    //     // if ($user->role === 'staff') {
    //     //     $query->where('created_by', $user->id);
    //     // } else {
    //     //     $query->where('branch_id', $branchId);
    //     // }

    //     $customers = $query->orderByDesc('id')->get()->map(function ($customer) {
    //         return [
    //             'id'                => $customer->id,
    //             'name'              => $customer->name,
    //             'email'             => $customer->email,
    //             'phone'             => $customer->phone,
    //             'gst_number'        => $customer->gst_number,
    //             'pan_number'        => $customer->pan_number,
    //             'profile_image'     => $customer->profile_image,
    //             'profile_image_url' => $customer->profile_image_url,
    //             'country'           => optional($customer->details)->country ?? '',
    //             'city'              => optional($customer->details)->city ?? '',
    //         ];
    //     });

    //     return response()->json(['status' => true, 'data' => $customers]);
    // }

    public function getAllCustomer(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $this->resolveBranchId($user, $request);

        $perPage = $request->input('per_page', 10); // default 10
        $search  = $request->input('search', '');

        $query = User::select('id', 'name', 'customer_code', 'email', 'phone', 'profile_image', 'gst_number', 'pan_number')
            ->where('role', 'customer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->with('details:country,user_id,city');

        // Warehouse/Account: all branch customers; Sales/Other/unset: own only
        StaffDepartmentScope::applyStaffCreatedByScope($query, $user);
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('gst_number', 'LIKE', "%{$search}%")
                    ->orWhere('pan_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('details', function ($subQ) use ($search) {
                        $subQ->where('country', 'LIKE', "%{$search}%")
                            ->orWhere('city', 'LIKE', "%{$search}%");
                    });
            });
        }

        $customers = $query->orderByDesc('id')->paginate($perPage);

        // Transform data
        $transformed = $customers->map(function ($customer) {
            return [
                'id'                => $customer->id,
                'name'              => $customer->name,
                'customer_code'     => $customer->customer_code,
                'email'             => $customer->email,
                'phone'             => $customer->phone,
                'gst_number'        => $customer->gst_number,
                'pan_number'        => $customer->pan_number,
                'profile_image'     => $customer->profile_image,
                'profile_image_url' => $customer->profile_image_url,
                'country'           => optional($customer->details)->country ?? '',
                'city'              => optional($customer->details)->city ?? '',
            ];
        });

        return response()->json([
            'status'     => true,
            'data'       => $transformed,
            'pagination' => [
                'total'        => $customers->total(),
                'per_page'     => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'from'         => $customers->firstItem(),
                'to'           => $customers->lastItem(),
            ],
        ]);
    }

    /* ---------------------------------------------------------
       Get Customer By ID
    --------------------------------------------------------- */
    public function getCustomerById($id)
    {
        $customer = User::find($id);

        return $customer
            ? response()->json(['status' => true, 'customer' => $customer])
            : response()->json(['status' => false, 'error' => 'Customer not found'], 404);
    }

    /* ---------------------------------------------------------
       Create Customer
    --------------------------------------------------------- */
    public function getNextCustomerCode()
    {
        $lastCustomer = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->whereNotNull('customer_code')
            ->where('customer_code', '!=', '')
            ->orderByRaw("LENGTH(customer_code) DESC, customer_code DESC")
            ->first();

        $nextNumber = 1;
        if ($lastCustomer && preg_match('/(\d+)$/', $lastCustomer->customer_code, $m)) {
            $nextNumber = (int)$m[1] + 1;
        }
        $nextCustomerCode = 'CUST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['status' => true, 'customer_code' => $nextCustomerCode]);
    }

    public function createCustomer(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $branchId = $this->resolveBranchId($authUser, $request);

        $stateParts = explode(' - ', $request->state_code, 2);
        $request->merge([
            'state_code' => $stateParts[0],
            'state_name' => $request->input('state_name') ?: ($stateParts[1] ?? null),
        ]);

        $validator = Validator::make($request->all(), [

            'customer_name' => [
                'required',
                'string',
                'max:80',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:80',
            ],

            'customer_code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email'         => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId)
                        ->where('role', 'customer')
                        ->where('isDeleted', 0);
                }),
            ],

            'phone'         => [
                'nullable',
                Rule::unique('users', 'phone')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId)
                        ->where('role', 'customer')
                        ->where('isDeleted', 0);
                }),
            ],

            'alternate_phone' => [
                'nullable',
            ],

            'gst_number'    => [
                'nullable',
                'string',
                'size:15',
            ],

            'pan_number'    => [
                'nullable',
                'string',
                'size:10',
            ],

            'country'       => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',

            'state_code' => 'nullable|digits_between:1,3',

            'state_name'    => 'nullable|string|max:100',

            'address'       => 'nullable|string|max:500',
            'delivery_address' => 'nullable|string|max:500',

            'avatar'        => [
                'nullable',
                'file',
                'image',                       // ensures image only
                'mimes:jpeg,jpg,png,webp,gif', // restrict extensions
                'max:2048',                    // 2MB
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $createdCustomer = DB::transaction(function () use ($request, $authUser, $branchId) {

            $customer = new User();
            $customer->fill([
                'name'          => $request->customer_name,
                'company_name'  => $request->company_name,
                'customer_code' => $request->customer_code,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'alternate_phone' => $request->alternate_phone,
                'gst_number'    => $request->gst_number,
                'pan_number'    => $request->pan_number,
                'role'          => 'customer',
                'state_code'    => $request->state_code,
                'state_name'    => $request->state_name,
                'branch_id'     => $branchId,
                'created_by'    => $authUser->id,
                'status'     => 1,
            ]);

            if ($request->hasFile('avatar')) {

                if (! $request->file('avatar')->isValid()) {
                    return null;
                }

                $customer->profile_image = $request->file('avatar')
                    ->store('customer', 'public');
            }

            $customer->save();

            UserDetail::create([
                'user_id' => $customer->id,
                'country' => $request->country,
                'city'    => $request->city,
                'address' => $request->address,
                'delivery_address' => $request->delivery_address,
            ]);
            // 🔔 CREATE NOTIFICATION
            Notification::create([
                'user_id'   => $customer->id, // admin who created
                'type'      => 'customer',
                'title'     => 'New Customer Created',
                'message'   => $customer->name . ' has been added successfully.',
                'link'      => '/customer-view/' . $customer->id,
                'is_read'   => 0,
                'is_sound'  => 0,
                'branch_id' => $branchId,
            ]);

            return $customer;
        });

        return response()->json([
            'status'      => true,
            'message'     => 'Customer created successfully!',
            'customer_id' => $createdCustomer ? $createdCustomer->id : null,
        ]);
    }

    public function getCustomer($id)
    {
        // Find customer and include details
        $customer = User::with('details')->find($id);

        if (! $customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        return response()->json([
            'status'   => true,
            'customer' => $customer,
        ]);
    }

    /* ---------------------------------------------------------
       Delete Customer (Soft Delete)
    --------------------------------------------------------- */
    public function deleteCustomer(Request $request, $id)
    {
        $customer = User::find($id);

        if (! $customer || $customer->role !== 'customer') {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        if (Order::where('user_id', $id)->where('isDeleted', 0)->exists()) {
            return response()->json(['status' => false, 'error' => 'Customer has orders'], 400);
        }

        if (CustomInvoice::where('customer_id', $id)->where('isDeleted', 0)->exists()) {
            return response()->json(['status' => false, 'error' => 'Customer linked to invoice'], 400);
        }

        DB::transaction(function () use ($customer, $id) {
            UserDetail::where('user_id', $id)->update(['isDeleted' => 1]);
            $customer->update(['isDeleted' => 1]);
        });

        return response()->json(['status' => true, 'message' => 'Customer deleted successfully!']);
    }

    /* ---------------------------------------------------------
       Update Customer
    --------------------------------------------------------- */
    public function updateCustomer(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $branchId = $customer->branch_id;

        $stateParts = explode(' - ', $request->state_code, 2);
        $request->merge([
            'state_code' => $stateParts[0],
            'state_name' => $request->input('state_name') ?: ($stateParts[1] ?? null),
        ]);

        $validator = Validator::make($request->all(), [

            'customer_name' => [
                'required',
                'string',
                'max:80',
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:80',
            ],

            'customer_code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email'         => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($id)->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId)
                        ->where('role', 'customer')
                        ->where('isDeleted', 0);
                }),
            ],

            'phone'         => [
                'nullable',
                Rule::unique('users', 'phone')->ignore($id)->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId)
                        ->where('role', 'customer')
                        ->where('isDeleted', 0);
                }),
            ],

            'alternate_phone' => [
                'nullable',
            ],

            'gst_number'    => 'nullable|string|max:15',

            'pan_number'    => 'nullable|string|max:10',

            'country'       => 'nullable|string|max:100',

            'city'          => 'nullable|string|max:100',

            'address'       => 'nullable|string|max:500',
            'delivery_address' => 'nullable|string|max:500',

            'state_code'    => 'nullable|numeric|digits_between:1,3',

            'state_name'    => 'nullable|string|max:100',

            'avatar'        => [
                'nullable',
                'file',
                'image',                       // ensures only images
                'mimes:jpeg,png,jpg,webp,gif', // blocks pdf, csv, xls
                'max:2048',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $customer->update([
            'name'         => $request->customer_name,
            'company_name' => $request->company_name,
            'customer_code'=> $request->customer_code,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'alternate_phone' => $request->alternate_phone,
            'gst_number'   => $request->gst_number,
            'pan_number'   => $request->pan_number,
            'state_code'   => $request->state_code,
            'state_name'   => $request->state_name,
        ]);

        // if ($request->hasFile('avatar')) {
        //     $customer->update([
        //         'profile_image' => $request->file('avatar')->store('customer', 'public'),
        //     ]);
        // }
        if ($request->hasFile('avatar')) {

            $file = $request->file('avatar');

            if (! $file->isValid()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid image file',
                ], 422);
            }

            $path = $file->store('customer', 'public');

            $customer->update([
                'profile_image' => $path,
            ]);
        }

        UserDetail::updateOrCreate(
            ['user_id' => $id],
            [
                'country' => $request->country,
                'city'    => $request->city,
                'address' => $request->address,
                'delivery_address' => $request->delivery_address,
            ]
        );

        return response()->json(['status' => true, 'message' => 'Customer updated successfully!']);
    }

    /* ---------------------------------------------------------
       Customer Profile
    --------------------------------------------------------- */
    public function getCustomerProfile($id)
    {
        $user = User::with('userDetail')->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'name'          => $user->name,
            'company_name'  => $user->company_name,
            'customer_code' => $user->customer_code,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'gst_number'    => $user->gst_number,
            'pan_number'    => $user->pan_number,
            'role'          => $user->role,
            'profile_image' => $user->profile_image,
            'state_code'    => $user->state_code,
            'address'       => optional($user->userDetail)->address ?? 'N/A',
            'delivery_address' => optional($user->userDetail)->delivery_address ?? 'N/A',
            'city'          => optional($user->userDetail)->city ?? 'N/A',
            'country'       => optional($user->userDetail)->country ?? 'N/A',
        ]);
    }

    /* ---------------------------------------------------------
       Import Customers (CSV / Excel)
    --------------------------------------------------------- */
    public function importCustomers(Request $request)
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

        $branchId = $this->resolveBranchId($authUser, $request);
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $headers = [];
        $headerRowNumber = null;

        foreach ($rows as $rowNumber => $candidateHeaderRow) {
            $candidateHeaders = [];
            foreach ($candidateHeaderRow as $column => $heading) {
                $normalized = $this->normalizeImportHeader((string) $heading);
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
                'message' => 'Could not find the header row. Please include Customer Name column.',
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
            foreach ($rows as $index => $row) {
                if ((int) $index <= (int) $headerRowNumber) {
                    continue;
                }

                $lineNumber = is_numeric($index) ? ((int) $index) : ($processed + $skipped + 2);
                $mapped = $this->mapImportRow($row, $headers);

                if ($this->isEmptyImportRow($mapped)) {
                    continue;
                }

                $name = $this->importStringValue($mapped, 'name');
                if ($name === '') {
                    $skipped++;
                    $errors[] = "Row {$lineNumber}: Customer Name is required.";
                    continue;
                }

                $result = $this->upsertImportedCustomer($mapped, $branchId, $authUser);
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
            Log::error('Customer import failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Import failed. Please check the file and try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Customers imported successfully.',
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'unchanged' => $unchanged,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    private function upsertImportedCustomer(array $mapped, int $branchId, $authUser): array
    {
        $name = $this->importStringValue($mapped, 'name');
        $phone1 = $this->normalizeImportPhone($this->importStringValue($mapped, 'phone_1'));
        $phone2 = $this->normalizeImportPhone($this->importStringValue($mapped, 'phone_2'));
        $email = $this->normalizeImportEmail($this->importStringValue($mapped, 'email'));
        $pan = strtoupper($this->importStringValue($mapped, 'pan_number'));
        $gstin = strtoupper($this->importStringValue($mapped, 'gst_number'));
        $membership = $this->importStringValue($mapped, 'membership_no');
        $customerCode = $this->importStringValue($mapped, 'customer_code');

        $customer = $this->findImportedCustomer(
            $branchId,
            $mapped,
            $name,
            $phone1,
            $email,
            $pan,
            $gstin,
            $membership,
            $customerCode
        );
        $created = false;

        if (! $customer) {
            $customer = new User([
                'role' => 'customer',
                'branch_id' => $branchId,
                'created_by' => $authUser->id,
                'status' => 1,
            ]);
            $created = true;
        }

        $customer->name = $name;
        $this->assignImportString($customer, 'company_name', $mapped, 'company_name');
        $this->assignImportString($customer, 'state_name', $mapped, 'state');
        $this->assignImportString($customer, 'customer_category', $mapped, 'category');
        $this->assignImportString($customer, 'pan_status', $mapped, 'pan_status');
        $this->assignImportString($customer, 'gstin_status', $mapped, 'gstin_status');
        $this->assignImportString($customer, 'gender', $mapped, 'gender');
        $this->assignImportString($customer, 'tin_number', $mapped, 'tin_number');

        if ($membership !== '') {
            $customer->membership_no = $membership;
        }

        if ($customerCode !== '') {
            $customer->customer_code = $customerCode;
        }

        if ($phone1 !== '' && $this->canUseImportValue('phone', $phone1, $customer->id)) {
            $customer->phone = $phone1;
        }

        if ($phone2 !== '') {
            $customer->alternate_phone = $phone2;
        }

        if ($email !== '' && $this->canUseImportValue('email', $email, $customer->id)) {
            $customer->email = $email;
        }

        if ($pan !== '') {
            $customer->pan_number = $pan;
        }

        if ($gstin !== '') {
            $customer->gst_number = $gstin;
        }

        if ($this->hasImportValue($mapped, 'age')) {
            $age = preg_replace('/[^0-9]/', '', (string) $mapped['age']);
            if ($age !== '') {
                $customer->age = (int) $age;
            }
        }

        foreach (['dob' => 'dob', 'doa' => 'doa', 'customer_since' => 'customer_since'] as $field => $modelField) {
            $date = $this->parseImportDate($this->importStringValue($mapped, $field));
            if ($date !== null) {
                $customer->{$modelField} = $date;
            }
        }

        if ($this->hasImportValue($mapped, 'status')) {
            $customer->status = $this->mapImportStatus((string) $mapped['status']);
        }

        $wasDirty = $customer->isDirty();
        $customer->save();

        $detail = UserDetail::firstOrNew(['user_id' => $customer->id]);
        $this->assignImportString($detail, 'address', $mapped, 'address_line1');
        $this->assignImportString($detail, 'address_line2', $mapped, 'address_line2');
        $this->assignImportString($detail, 'address_line3', $mapped, 'address_line3');
        $this->assignImportString($detail, 'pin_code', $mapped, 'pin_code');
        $this->assignImportString($detail, 'city', $mapped, 'city');

        $detailWasDirty = $detail->isDirty();
        $detail->save();

        return [
            'created' => $created,
            'updated' => ! $created && ($wasDirty || $detailWasDirty),
        ];
    }

    private function findImportedCustomer(
        int $branchId,
        array $mapped,
        string $name,
        string $phone,
        string $email,
        string $pan,
        string $gstin,
        string $membership,
        string $customerCode
    ): ?User {
        $baseQuery = User::where('role', 'customer')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0);

        foreach ([
            ['customer_code', $customerCode],
            ['membership_no', $membership],
            ['phone', $phone],
            ['email', $email],
            ['pan_number', $pan],
            ['gst_number', $gstin],
        ] as [$column, $value]) {
            if ($value === '') {
                continue;
            }

            $found = (clone $baseQuery)->where($column, $value)->first();
            if ($found) {
                return $found;
            }
        }

        $addressLine1 = $this->importStringValue($mapped, 'address_line1');
        $city = $this->importStringValue($mapped, 'city');

        if ($name === '' || ($addressLine1 === '' && $city === '' && $phone === '')) {
            return null;
        }

        return (clone $baseQuery)
            ->where('name', $name)
            ->when($phone !== '', fn ($q) => $q->where('phone', $phone))
            ->when($phone === '' && ($addressLine1 !== '' || $city !== ''), function ($q) use ($addressLine1, $city) {
                $q->whereHas('details', function ($query) use ($addressLine1, $city) {
                    if ($addressLine1 !== '') {
                        $query->where('address', $addressLine1);
                    }
                    if ($city !== '') {
                        $query->where('city', $city);
                    }
                });
            })
            ->first();
    }

    private function normalizeImportHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        $header = trim((string) $header, '_');

        return match ($header) {
            's_no', 'sn', 'sr_no', 'serial_no' => 'sn',
            'customer_name', 'customer', 'party_name', 'party', 'name' => 'name',
            'company_name', 'company' => 'company_name',
            'membership', 'membership_no', 'membership_number', 'membership_hash' => 'membership_no',
            'customer_code', 'cust_code', 'code' => 'customer_code',
            'customer_since', 'since' => 'customer_since',
            'address_line1', 'address_line_1', 'address1', 'address_1', 'address' => 'address_line1',
            'address_line2', 'address_line_2', 'address2', 'address_2' => 'address_line2',
            'address_line3', 'address_line_3', 'address3', 'address_3' => 'address_line3',
            'pin_code', 'pincode', 'pin' => 'pin_code',
            'city_town', 'city', 'town' => 'city',
            'state', 'state_name' => 'state',
            'phone_1', 'phone1', 'phone_no_1', 'phone_number_1', 'mobile_1', 'mobile1' => 'phone_1',
            'phone', 'phone_no', 'phone_number', 'mobile', 'mobile_no' => 'phone_1',
            'phone_2', 'phone2', 'phone_no_2', 'phone_number_2', 'mobile_2', 'mobile2', 'alternate_phone' => 'phone_2',
            'email', 'email_id', 'email_address' => 'email',
            'category', 'customer_category' => 'category',
            'age' => 'age',
            'dob', 'date_of_birth', 'birth_date' => 'dob',
            'doa', 'date_of_anniversary', 'anniversary' => 'doa',
            'pan', 'pan_no', 'pan_number' => 'pan_number',
            'pan_status' => 'pan_status',
            'tin', 'tin_no', 'tin_number' => 'tin_number',
            'gstin_status', 'gst_status' => 'gstin_status',
            'gstin', 'gst_number', 'gst_no' => 'gst_number',
            'gender' => 'gender',
            'status' => 'status',
            default => $header,
        };
    }

    private function mapImportRow(array $row, array $headers): array
    {
        $mapped = [];
        foreach ($headers as $column => $field) {
            $mapped[$field] = $row[$column] ?? null;
        }

        return $mapped;
    }

    private function isEmptyImportRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function hasImportValue(array $mapped, string $field): bool
    {
        return array_key_exists($field, $mapped) && trim((string) $mapped[$field]) !== '';
    }

    private function importStringValue(array $mapped, string $field): string
    {
        return $this->hasImportValue($mapped, $field) ? trim((string) $mapped[$field]) : '';
    }

    private function assignImportString($model, string $modelField, array $mapped, string $importField): void
    {
        if (! $this->hasImportValue($mapped, $importField)) {
            return;
        }

        $model->{$modelField} = trim((string) $mapped[$importField]);
    }

    private function normalizeImportPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen((string) $digits) > 10 && str_starts_with((string) $digits, '91')) {
            $digits = substr((string) $digits, -10);
        }

        return (string) $digits;
    }

    private function normalizeImportEmail(string $email): string
    {
        $email = strtolower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function canUseImportValue(string $column, string $value, ?int $currentUserId): bool
    {
        return ! User::where($column, $value)
            ->when($currentUserId, fn ($query) => $query->where('id', '!=', $currentUserId))
            ->exists();
    }

    private function parseImportDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function mapImportStatus(string $status): int
    {
        $status = strtolower(trim($status));

        return in_array($status, ['inactive', 'disabled', 'blocked', 'closed', '0', 'no'], true) ? 0 : 1;
    }
}
