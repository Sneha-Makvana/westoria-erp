<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Purchases;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{

    public function getAllSupplier(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $userBranchId = $user->id ?? null;

        if ($user->role === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif (! empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $perPage = $request->get('per_page', 10); // default 10
        $search  = $request->get('search', '');

        $query = User::select('id', 'name', 'email', 'phone', 'profile_image', 'gst_number', 'pan_number')
            ->where('role', 'vendor')
            ->where('isDeleted', 0)
            ->where('branch_id', $userBranchId)
            ->with('details:country,user_id,city');

        // Apply search if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('gst_number', 'LIKE', "%{$search}%")
                    ->orWhere('pan_number', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('id', 'desc')->paginate($perPage);

        // Transform data
        $data = $suppliers->map(function ($supplier) {
            return [
                'id'                => $supplier->id,
                'name'              => $supplier->name,
                'email'             => $supplier->email,
                'phone'             => $supplier->phone,
                'profile_image'     => $supplier->profile_image,
                'profile_image_url' => $supplier->profile_image_url,
                'country'           => $supplier->details ? $supplier->details->country : '',
                'city'              => $supplier->details ? $supplier->details->city : '',
                'gst_number'        => $supplier->gst_number,
                'pan_number'        => $supplier->pan_number,
            ];
        });

        return response()->json([
            'status'     => true,
            'data'       => $data,
            'pagination' => [
                'current_page' => $suppliers->currentPage(),
                'last_page'    => $suppliers->lastPage(),
                'per_page'     => $suppliers->perPage(),
                'total'        => $suppliers->total(),
                'from'         => $suppliers->firstItem(),
                'to'           => $suppliers->lastItem(),
            ],
        ]);
    }

    public function getSupplierById($id)
    {
        $customer = User::find($id); // Assuming you have a User model
        if ($customer) {
            return response()->json(['status' => true, 'customer' => $customer], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }
    }
    public function createSupplier(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $userId   = $authUser->id;

        // 🔹 Decide branch ID properly
        if ($authUser->role == 'staff' && $authUser->branch_id) {
            $userBranchId = $authUser->branch_id; // staff uses their branch_id
        } elseif ($authUser->role == 'sub-admin') {
            $userBranchId = $authUser->id; // sub-admin uses own id
        } elseif ($authUser->role == 'admin' && ! empty($request->selectedSubAdminId)) {
            $userBranchId = (int) $request->selectedSubAdminId; // admin chooses sub-admin
        } else {
            $userBranchId = $authUser->id; // fallback to logged in user's id
        }

           $stateParts = explode(' - ', $request->state_code, 2);
           $request->merge([
                'state_code' => $stateParts[0],
                'state_name' => $request->input('state_name') ?: ($stateParts[1] ?? null),
            ]);

        $validator = Validator::make($request->all(), [

            'name'       => [
                'required',
                'string',
                'max:80',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:80',
            ],

            'email'      => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->where(function ($query) use ($userBranchId) {
                    return $query->where('branch_id', $userBranchId)
                        ->where('role', 'vendor')
                        ->where('isDeleted', 0);
                }),
            ],

            'phone'      => [
                'required',
                'digits:10', // exactly 10 digits
                'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'phone')->where(function ($query) use ($userBranchId) {
                    return $query->where('branch_id', $userBranchId)
                        ->where('role', 'vendor')
                        ->where('isDeleted', 0);
                }),
            ],

            'country'    => 'nullable|string|max:100',
            'city'       => 'nullable|string|max:100',

            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',

            'address'    => 'nullable|string|max:500',

            'state_code' => 'nullable|digits_between:1,3',

            'state_name' => 'nullable|string|max:100',

            'avatar'     => [
                'nullable',
                'file',
                'image', // ensures file is image
                'mimes:jpeg,png,jpg,webp,gif',
                'max:2048',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Create user
        $customer             = new User();
        $customer->name          = $request->name;
        $customer->company_name  = $request->company_name;
        $customer->email         = $request->email;
        $customer->phone         = $request->phone;
        $customer->gst_number    = $request->gst_number;
        $customer->pan_number    = $request->pan_number;
        $customer->state_code    = $request->state_code;
        $customer->state_name    = $request->state_name;
        if ($request->hasFile('avatar')) {
            $path                    = $request->file('avatar')->store('vendor', 'public');
            $customer->profile_image = $path;
        }
        $customer->role       = 'vendor';
        $customer->branch_id  = $userBranchId ?? $userId;
        $customer->created_by = $authUser->id;
        $customer->status     = 1;
        $customer->save(); // Save user first to get its ID

        // Now create user details using the saved user's ID
        $customerDetail          = new UserDetail();
        $customerDetail->user_id = $customer->id; // Use the newly created user's ID
        $customerDetail->country = $request->country;
        $customerDetail->city    = $request->city;
        $customerDetail->address = $request->address;
        
        $customerDetail->supplier_since = $request->supplier_since;
        $customerDetail->address_line2 = $request->address_line2;
        $customerDetail->address_line3 = $request->address_line3;
        $customerDetail->pin_code = $request->pin_code;
        $customerDetail->phone_2 = $request->phone_2;
        $customerDetail->supplier_category = $request->supplier_category;
        $customerDetail->tin_number = $request->tin_number;
        $customerDetail->credit_days = $request->credit_days;
        $customerDetail->pan_status = $request->pan_status;
        $customerDetail->gstin_status = $request->gstin_status;
        $customerDetail->account_group = $request->account_group;
        $customerDetail->tan_status = $request->tan_status;
        $customerDetail->tan_number = $request->tan_number;
        $customerDetail->msme_status = $request->msme_status;
        $customerDetail->msme_number = $request->msme_number;
        $customerDetail->status = $request->status;
        $customerDetail->save();

        // 🔔 CREATE NOTIFICATION FOR VENDOR/SUPPLIER CREATION
        Notification::create([
            'user_id'   => $customer->id, // user who created the vendor
            'type'      => 'vendor',
            'title'     => 'New Vendor Created',
            'message'   => $customer->name . ' has been added as a vendor successfully.',
            'link'      => '/vendor-view/' . $customer->id,
            'is_read'   => 0,
            'is_sound'  => 0,
            'branch_id' => $userBranchId ?? $userId,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Vendor created successfully!',
            'vendor'  => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'phone'  => $customer->phone,
            ],
        ]);
    }

    public function deleteSupplier(Request $request, $id)
    {
        $supplier = User::find($id);

        if (! $supplier || $supplier->role !== 'vendor') {
            return response()->json(['status' => false, 'error' => 'Vendor not found'], 404);
        }

        // Check if vendor is associated with any purchase invoices
        $hasPurchases = Purchases::where('vendor_id', $id)->where('isDeleted', 0)->exists();

        if ($hasPurchases) {
            return response()->json([
                'status' => false,
                'error'  => 'This vendor cannot be deleted because they are associated with purchase.',
            ], 400);
        }

        try {
                                                                           // Delete related user details (soft delete or hard delete as per your logic)
            UserDetail::where('user_id', $id)->update(['isDeleted' => 1]); // Optional if UserDetail has isDeleted

            // Soft delete the supplier by setting isDeleted = 1
            $supplier->isDeleted = 1;
            $supplier->save();

            return response()->json([
                'status'  => true,
                'message' => 'Vendor deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSupplier($id)
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

    public function updateSupplier(Request $request, $id)
    {
        $userBranchId = User::where('id', $id)->value('branch_id');
        $stateParts = explode(' - ', $request->state_code, 2);
        $request->merge([
            'state_code' => $stateParts[0],
            'state_name' => $request->input('state_name') ?: ($stateParts[1] ?? null),
        ]);
        // Validate input data
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
            'email'         => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($id)->where(function ($query) use ($userBranchId) {
                    return $query->where('branch_id', $userBranchId)
                        ->where('role', 'vendor')
                        ->where('isDeleted', 0);
                }),
            ],

            'phone'         => [
                'required',
                'digits:10', // exactly 10 digits
                'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'phone')->ignore($id)->where(function ($query) use ($userBranchId) {
                    return $query->where('branch_id', $userBranchId)
                        ->where('role', 'vendor')
                        ->where('isDeleted', 0);
                }),
            ],

            'country'       => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',

            'gst_number'    => 'nullable|string|max:15',
            'pan_number'    => 'nullable|string|max:10',

            'address'       => 'nullable|string|max:500',

            'state_code'    => 'nullable|numeric|digits_between:1,3',

            'state_name'    => 'nullable|string|max:100',

            'avatar'        => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:2048',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Find the user
        $customer = User::find($id);
        if (! $customer) {
            return response()->json(['status' => false, 'error' => 'Customer not found'], 404);
        }

        // Update user details
        $customer->name       = $request->customer_name;
        $customer->company_name = $request->company_name;
        $customer->state_code = $request->state_code;
        $customer->state_name = $request->state_name;
        $customer->email      = $request->email;
        $customer->phone      = $request->phone;
        $customer->gst_number = $request->gst_number;
        $customer->pan_number = $request->pan_number;

        // Handle profile image update
        if ($request->hasFile('avatar')) {
            $path                    = $request->file('avatar')->store('vendor', 'public');
            $customer->profile_image = $path;
        }

        $customer->save();

        // Update related user details
        $customerDetail = UserDetail::where('user_id', $id)->first();
        if ($customerDetail) {
            $customerDetail->country = $request->country;
            $customerDetail->city    = $request->city;
            $customerDetail->address = $request->address;
            
            $customerDetail->supplier_since = $request->supplier_since;
            $customerDetail->address_line2 = $request->address_line2;
            $customerDetail->address_line3 = $request->address_line3;
            $customerDetail->pin_code = $request->pin_code;
            $customerDetail->phone_2 = $request->phone_2;
            $customerDetail->supplier_category = $request->supplier_category;
            $customerDetail->tin_number = $request->tin_number;
            $customerDetail->credit_days = $request->credit_days;
            $customerDetail->pan_status = $request->pan_status;
            $customerDetail->gstin_status = $request->gstin_status;
            $customerDetail->account_group = $request->account_group;
            $customerDetail->tan_status = $request->tan_status;
            $customerDetail->tan_number = $request->tan_number;
            $customerDetail->msme_status = $request->msme_status;
            $customerDetail->msme_number = $request->msme_number;
            $customerDetail->status = $request->status;
            $customerDetail->save();
        }

        return response()->json(['status' => true, 'message' => 'Customer updated successfully!']);
    }

    public function getVendorProfile($id)
    {
        $user = User::with('userDetail')->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'name'          => $user->name,
            'company_name'  => $user->company_name ?? '',
            'email'         => $user->email,
            'phone'         => $user->phone,
            'gst_number'    => $user->gst_number ?? '',
            'pan_number'    => $user->pan_number ?? '',
            'role'          => $user->role,
            'state_code'    => $user->state_code ?? '',
            'state_name'    => $user->state_name ?? '',
            'profile_image' => $user->profile_image,
            'address'       => $user->userDetail->address ?? '',
            'city'          => $user->userDetail->city ?? '',
            'country'       => $user->userDetail->country ?? '',
        ]);
    }
    
    public function fetch(Request $request)
    {
        $gst = strtoupper(preg_replace('/\s+/', '', (string) $request->input('gst_number', '')));

        if ($gst === '') {
            return response()->json(['error' => 'GST number is required'], 400);
        }

        if (! preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/', $gst)) {
            return response()->json(['error' => 'Invalid GST number format'], 400);
        }

        $cacheKey = 'gst_lookup_' . $gst;
        $cached = \Cache::get($cacheKey);
        if (is_array($cached) && $this->isValidGstLookupPayload($cached, $gst)) {
            $cached['source'] = $cached['source'] ?? 'cache';
            return response()->json($cached);
        }
        if (is_array($cached)) {
            // Drop poisoned cache (e.g. Appyflow demo sample stored under wrong GSTIN)
            \Cache::forget($cacheKey);
        }

        // 1) External providers (Appyflow / GST portal / ClearTax)
        $remote = $this->fetchGstFromRemoteProvider($gst);
        if ($remote !== null && $this->isValidGstLookupPayload($remote, $gst)) {
            \Cache::put($cacheKey, $remote, now()->addDays(7));
            return response()->json($remote);
        }

        // 2) Local customer/vendor already saved with this GSTIN (must have useful data)
        $local = $this->fetchGstFromLocalUser($gst);
        if ($local !== null && ! empty($local['legal_name'])) {
            return response()->json($local);
        }

        $useAppyflow = (bool) config('services.gst_lookup.use_appyflow', false);
        $hasKey = trim((string) config('services.gst_lookup.appyflow_key', '')) !== '';
        $message = 'Could not fetch full GST details from ClearTax (free lookup). PAN and state are filled from GSTIN — please enter name and address manually.';
        if ($useAppyflow && $hasKey) {
            $message = 'GST lookup unavailable (ClearTax blocked and Appyflow did not return valid data). Enter name and address manually, or fix GST_APPYFLOW_KEY_SECRET.';
        }

        return response()->json($this->gstinStructuralDetails($gst) + [
            'partial' => true,
            'message' => $message,
            'try_browser_cleartax' => true,
        ]);
    }

    private function fetchGstFromRemoteProvider(string $gst): ?array
    {
        // 0) Clear GSTIN verification API (token based)
        $clearApi = $this->fetchGstFromClearApi($gst);
        if ($clearApi !== null) {
            return $clearApi;
        }

        // 1) Free ClearTax (original ERP behaviour)
        $cleartax = $this->fetchGstFromCleartax($gst);
        if ($cleartax !== null) {
            return $cleartax;
        }

        // 2) GST portal public API (no key; often blocked on datacenter IPs)
        $portal = $this->fetchGstFromGstPortal($gst);
        if ($portal !== null) {
            return $portal;
        }

        // 3) Optional paid Appyflow (off by default — set GST_USE_APPYFLOW=true)
        return $this->fetchGstFromAppyflow($gst);
    }

    private function fetchGstFromClearApi(string $gst): ?array
    {
        $token = trim((string) config('services.gst_lookup.clear_api_token', ''));
        $url = trim((string) config('services.gst_lookup.clear_api_url', ''));
        if ($token === '' || $url === '') {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withHeaders(['X-Cleartax-Auth-Token' => $token])
                ->get($url, ['gstin' => $gst]);

            if (! $response->successful()) {
                \Log::warning('Clear GSTIN API returned error', [
                    'gst' => $gst,
                    'status' => $response->status(),
                    'body' => mb_substr((string) $response->body(), 0, 500),
                ]);

                return null;
            }

            $parsed = $this->mapTaxpayerPayload($response->json(), $gst);
            if ($parsed !== null && $this->isValidGstLookupPayload($parsed, $gst)) {
                $parsed['source'] = 'clear_api';

                return $parsed;
            }
        } catch (\Throwable $e) {
            \Log::warning('Clear GSTIN API lookup failed', [
                'gst' => $gst,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function fetchGstFromCleartax(string $gst): ?array
    {
        $base = rtrim((string) config('services.gst_lookup.cleartax_url', 'https://cleartax.in/f/compliance-report'), '/');
        $urls = ["{$base}/{$gst}", "{$base}/{$gst}/"];

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Accept' => 'application/json, text/plain, */*',
                        'Accept-Language' => 'en-US,en;q=0.9',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                        'Referer' => 'https://cleartax.in/s/gst-number-search/',
                        'Origin' => 'https://cleartax.in',
                    ])
                    ->get($url);

                if (! $response->successful()) {
                    continue;
                }

                $json = $response->json();
                if (! is_array($json) || empty($json['taxpayerInfo'])) {
                    continue;
                }

                $parsed = $this->mapTaxpayerPayload($json, $gst);
                if ($parsed !== null && $this->isValidGstLookupPayload($parsed, $gst)) {
                    $parsed['source'] = 'cleartax';

                    return $parsed;
                }
            } catch (\Throwable $e) {
                \Log::warning('ClearTax GST lookup failed', [
                    'gst' => $gst,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    private function fetchGstFromGstPortal(string $gst): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json, text/plain, */*',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Referer' => 'https://services.gst.gov.in/',
                    'Origin' => 'https://services.gst.gov.in',
                ])
                ->get("https://services.gst.gov.in/services/api/search/taxpayerDetails/{$gst}");

            if ($response->successful() && str_contains((string) $response->header('Content-Type'), 'json')) {
                $parsed = $this->mapTaxpayerPayload($response->json(), $gst);
                if ($parsed !== null && $this->isValidGstLookupPayload($parsed, $gst)) {
                    $parsed['source'] = 'gst_portal';

                    return $parsed;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('GST portal lookup failed', [
                'gst' => $gst,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function fetchGstFromAppyflow(string $gst): ?array
    {
        if (! (bool) config('services.gst_lookup.use_appyflow', false)) {
            return null;
        }

        $key = trim((string) config('services.gst_lookup.appyflow_key', ''));
        $url = trim((string) config('services.gst_lookup.url', 'https://appyflow.in/api/verifyGST'));
        if ($key === '') {
            return null;
        }

        try {
            $payload = ['gstNo' => $gst, 'key_secret' => $key];
            $attempts = [
                fn () => Http::timeout(20)->acceptJson()->get($url, $payload),
                fn () => Http::timeout(20)->acceptJson()->asForm()->post($url, $payload),
                fn () => Http::timeout(20)->acceptJson()->asJson()->post($url, $payload),
            ];

            foreach ($attempts as $attempt) {
                $response = $attempt();
                if (! $response->successful()) {
                    continue;
                }

                $body = $this->normalizeGstApiResponse($response->json());
                if ($body === null) {
                    continue;
                }

                $parsed = $this->mapTaxpayerPayload($body, $gst);
                if ($parsed !== null && $this->isValidGstLookupPayload($parsed, $gst)) {
                    $parsed['source'] = 'appyflow';

                    return $parsed;
                }

                \Log::warning('GST Appyflow returned mismatched / demo payload', [
                    'requested' => $gst,
                    'returned' => $parsed['gstin'] ?? null,
                    'legal_name' => $parsed['legal_name'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('GST Appyflow lookup failed', [
                'gst' => $gst,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function fetchGstFromLocalUser(string $gst): ?array
    {
        try {
            $user = User::with('userDetail')
                ->where('isDeleted', 0)
                ->whereRaw("UPPER(REPLACE(gst_number, ' ', '')) = ?", [$gst])
                ->orderByDesc('id')
                ->first();
        } catch (\Throwable $e) {
            \Log::warning('Local GST lookup failed', [
                'gst' => $gst,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if (! $user) {
            return null;
        }

        $detail = $user->userDetail;
        $structural = $this->gstinStructuralDetails($gst);
        $stateName = trim((string) ($user->state_name ?: ($structural['state'] ?? '')));
        $address = trim((string) ($detail->address ?? ''));

        // Skip empty / incomplete local records (name alone without address is not enough for new GST fill)
        $name = trim((string) ($user->name ?? ''));
        if ($name === '') {
            return null;
        }

        // Prefer remote for full GST address; local only if address looks real (not just state)
        $looksLikeStateOnly = $address === ''
            || strcasecmp($address, $stateName) === 0
            || in_array(strtolower($address), ['india', 'gujarat', 'maharashtra'], true);
        if ($looksLikeStateOnly && trim((string) ($detail->city ?? '')) === '') {
            return null;
        }

        return [
            'gstin' => $gst,
            'company_name' => $user->company_name ?: $name,
            'legal_name' => $name,
            'business_type' => '',
            'taxpayer_type' => '',
            'status' => '',
            'registration_date' => '',
            'nature_of_business' => [],
            'primary_address' => $address,
            'city' => $detail->city ?? '',
            'state' => $stateName,
            'pincode' => trim((string) ($detail->pin_code ?? '')),
            'state_code' => $user->state_code ?: ($structural['state_code'] ?? ''),
            'country' => $detail->country ?? 'India',
            'last_updated' => '',
            'frequency' => '',
            'additional_addresses' => [],
            'pan' => $user->pan_number ?: ($structural['pan'] ?? ''),
            'source' => 'local',
        ];
    }

    private function normalizeGstApiResponse($data): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        if (
            (! empty($data['error']) && $data['error'] !== false)
            || (! empty($data['message']) && empty($data['taxpayerInfo']) && empty($data['data']['taxpayerInfo']))
        ) {
            $message = strtolower((string) ($data['message'] ?? ''));
            if ($message !== '' && (str_contains($message, 'key') || str_contains($message, 'secret') || str_contains($message, 'quota'))) {
                return null;
            }
        }

        if (isset($data['taxpayerInfo']) && is_array($data['taxpayerInfo'])) {
            return $data;
        }

        if (isset($data['data']['taxpayerInfo']) && is_array($data['data']['taxpayerInfo'])) {
            return ['taxpayerInfo' => $data['data']['taxpayerInfo']] + $data;
        }

        if (isset($data['result']['taxpayerInfo']) && is_array($data['result']['taxpayerInfo'])) {
            return ['taxpayerInfo' => $data['result']['taxpayerInfo']] + $data;
        }

        return $data;
    }

    private function normalizeGstPradrAddr(array $pradr): array
    {
        if ($pradr === []) {
            return [];
        }

        $addr = $pradr['addr'] ?? $pradr;
        if (! is_array($addr)) {
            return [];
        }

        foreach (['pncd', 'pincode', 'pin_code', 'pin', 'zip', 'dst', 'city', 'stcd', 'state', 'country'] as $key) {
            if (empty($addr[$key]) && ! empty($pradr[$key])) {
                $addr[$key] = $pradr[$key];
            }
        }

        return $addr;
    }

    private function extractGstPincode(array $addr): string
    {
        foreach (['pncd', 'pincode', 'pin_code', 'pin', 'zip', 'zipcode'] as $key) {
            $value = trim((string) ($addr[$key] ?? ''));
            if ($value !== '' && preg_match('/(\d{6})/', $value, $matches)) {
                return $matches[1];
            }
        }

        foreach ($addr as $value) {
            if (! is_scalar($value)) {
                continue;
            }
            $text = trim((string) $value);
            if ($text !== '' && preg_match('/\b(\d{6})\b/', $text, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }

    /**
     * Build full postal address from GSTN / Appyflow addr object (all non-empty parts).
     *
     * @return array{primary_address:string,city:string,state:string,pincode:string,country:string}
     */
    private function formatGstRegisteredAddress(array $addr): array
    {
        $city = trim((string) ($addr['dst'] ?? $addr['city'] ?? ''));
        $state = trim((string) ($addr['stcd'] ?? $addr['state'] ?? ''));
        $pincode = $this->extractGstPincode($addr);
        $country = trim((string) ($addr['country'] ?? 'India'));

        $segments = [];
        foreach (['flno', 'bno', 'bnm', 'st', 'loc', 'landMark', 'landmark', 'locality', 'street'] as $key) {
            $value = trim((string) ($addr[$key] ?? ''));
            if ($value !== '') {
                $segments[] = $value;
            }
        }

        // City + pin in address line; state stays in the separate State field.
        $tailParts = [];
        if ($city !== '') {
            $tailParts[] = $city;
        }
        if ($pincode !== '') {
            $tailParts[] = $pincode;
        }

        foreach ($tailParts as $tail) {
            if ($tail === '') {
                continue;
            }
            $already = false;
            foreach ($segments as $seg) {
                if (strcasecmp($seg, $tail) === 0 || stripos($seg, $tail) !== false) {
                    $already = true;
                    break;
                }
            }
            if (! $already) {
                $segments[] = $tail;
            }
        }

        $primaryAddress = implode(', ', $segments);
        $primaryAddress = preg_replace('/,\s*,+/', ', ', $primaryAddress);
        $primaryAddress = trim((string) $primaryAddress);

        return [
            'primary_address' => $primaryAddress,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'country' => $country !== '' ? $country : 'India',
        ];
    }

    private function mapTaxpayerPayload($data, string $gst): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        $gst = strtoupper(preg_replace('/\s+/', '', $gst));

        // Appyflow nests under taxpayerInfo; GST portal returns fields at root.
        $info = $data['taxpayerInfo']
            ?? $data['data']['taxpayerInfo']
            ?? $data['result']['taxpayerInfo']
            ?? null;

        if (! is_array($info) && ! empty($data['lgnm'])) {
            $info = $data;
        }

        if (! is_array($info)) {
            return null;
        }

        $responseGstin = strtoupper(preg_replace('/\s+/', '', (string) ($info['gstin'] ?? '')));
        // Appyflow demo / wrong key often returns sample GSTIN 03DOXPM4071K1ZE — never accept mismatch
        if ($responseGstin !== '' && $responseGstin !== $gst) {
            return null;
        }

        $companyName = trim((string) ($info['tradeNam'] ?? ($info['trade_name'] ?? ($info['lgnm'] ?? ''))));
        $legalName = trim((string) ($info['lgnm'] ?? ($info['legal_name'] ?? '')));
        if ($legalName === '' && $companyName === '') {
            return null;
        }

        // Known Appyflow documentation sample (always reject unless requested GSTIN is that sample)
        if ($this->isAppyflowDemoTaxpayer($legalName, $companyName, $responseGstin !== '' ? $responseGstin : $gst)
            && $gst !== '03DOXPM4071K1ZE') {
            return null;
        }

        $businessType = $info['ctb'] ?? '';
        $status = $info['sts'] ?? '';
        $registrationDate = $info['rgdt'] ?? '';
        $taxpayerType = $info['dty'] ?? '';
        $natureOfBusiness = $info['nba'] ?? [];
        $lastUpdated = $info['lstupdt'] ?? ($info['lastUpdated'] ?? '');
        $frequency = $info['frequencyType'] ?? ($info['filingFreq'] ?? ($data['compliance']['filingFrequency'] ?? ''));
        $pradr = $info['pradr'] ?? [];

        // Appyflow: pradr.addr{...} | GST portal: pradr{bno,bnm,...} flat
        $primaryAddr = $this->normalizeGstPradrAddr(is_array($pradr) ? $pradr : []);
        $formatted = $this->formatGstRegisteredAddress($primaryAddr);

        $additionalAddresses = [];
        if (isset($info['adadr']) && is_array($info['adadr'])) {
            foreach ($info['adadr'] as $addrItem) {
                $addr = $this->normalizeGstPradrAddr(is_array($addrItem) ? $addrItem : []);
                $formattedExtra = $this->formatGstRegisteredAddress($addr);
                $additionalAddresses[] = [
                    'name' => $addr['bnm'] ?? '',
                    'address' => $formattedExtra['primary_address'],
                    'city' => $formattedExtra['city'],
                    'state' => $formattedExtra['state'],
                    'pincode' => $formattedExtra['pincode'],
                    'nature' => $addrItem['ntr'] ?? '',
                ];
            }
        }

        // Always derive PAN / state from the REQUESTED GSTIN (never from a mismatched sample)
        $structural = $this->gstinStructuralDetails($gst);
        $stateName = trim((string) ($structural['state'] ?? ''));
        $addrState = trim((string) ($formatted['state'] ?? ''));
        if ($addrState !== '' && (strcasecmp($addrState, $stateName) === 0 || stripos($addrState, $stateName) !== false || stripos($stateName, $addrState) !== false)) {
            $stateName = $addrState;
        } elseif ($addrState !== '' && $stateName !== '' && strcasecmp($addrState, $stateName) !== 0) {
            // Address state contradicts GSTIN state code — treat as bad payload
            return null;
        }

        $city = $formatted['city'];
        if ($city === '') {
            $city = trim((string) ($primaryAddr['loc'] ?? ''));
        }

        return [
            'gstin' => $gst,
            'company_name' => $companyName !== '' ? $companyName : $legalName,
            'legal_name' => $legalName !== '' ? $legalName : $companyName,
            'business_type' => $businessType,
            'taxpayer_type' => $taxpayerType,
            'status' => $status,
            'registration_date' => $registrationDate,
            'nature_of_business' => $natureOfBusiness,
            'primary_address' => $formatted['primary_address'],
            'city' => $city,
            'state' => $stateName,
            'pincode' => $formatted['pincode'],
            'pin_code' => $formatted['pincode'],
            'state_code' => $structural['state_code'] ?? '',
            'country' => $formatted['country'] ?: ($info['country'] ?? 'India'),
            'last_updated' => $lastUpdated,
            'frequency' => $frequency,
            'additional_addresses' => $additionalAddresses,
            'pan' => $structural['pan'] ?? '',
            'source' => 'remote',
            'partial' => false,
        ];
    }

    /**
     * Ensure payload belongs to the GSTIN the user typed (blocks Appyflow demo sample).
     */
    private function isValidGstLookupPayload(array $payload, string $gst): bool
    {
        $gst = strtoupper(preg_replace('/\s+/', '', $gst));
        if ($gst === '' || empty($payload['legal_name'])) {
            return false;
        }

        $returnedGstin = strtoupper(preg_replace('/\s+/', '', (string) ($payload['gstin'] ?? '')));
        if ($returnedGstin !== '' && $returnedGstin !== $gst) {
            return false;
        }

        $pan = strtoupper(preg_replace('/\s+/', '', (string) ($payload['pan'] ?? '')));
        $expectedPan = substr($gst, 2, 10);
        if ($pan !== '' && $pan !== $expectedPan) {
            return false;
        }

        $stateCode = preg_replace('/\D+/', '', (string) ($payload['state_code'] ?? ''));
        $expectedState = substr($gst, 0, 2);
        if ($stateCode !== '' && str_pad($stateCode, 2, '0', STR_PAD_LEFT) !== $expectedState) {
            return false;
        }

        if ($this->isAppyflowDemoTaxpayer(
            (string) ($payload['legal_name'] ?? ''),
            (string) ($payload['company_name'] ?? ''),
            $returnedGstin !== '' ? $returnedGstin : $gst
        ) && $gst !== '03DOXPM4071K1ZE') {
            return false;
        }

        return true;
    }

    private function isAppyflowDemoTaxpayer(string $legalName, string $companyName, string $gstin): bool
    {
        $gstin = strtoupper(preg_replace('/\s+/', '', $gstin));
        if ($gstin === '03DOXPM4071K1ZE') {
            return true;
        }

        $legal = strtoupper(trim($legalName));
        $company = strtoupper(trim($companyName));

        return $legal === 'DISHANT MAHAJAN'
            || str_contains($company, 'APPYFLOW');
    }

    private function gstinStructuralDetails(string $gst): array
    {
        $stateCode = substr($gst, 0, 2);
        $pan = substr($gst, 2, 10);
        $stateNames = [
            '01' => 'Jammu and Kashmir', '02' => 'Himachal Pradesh', '03' => 'Punjab',
            '04' => 'Chandigarh', '05' => 'Uttarakhand', '06' => 'Haryana', '07' => 'Delhi',
            '08' => 'Rajasthan', '09' => 'Uttar Pradesh', '10' => 'Bihar', '11' => 'Sikkim',
            '12' => 'Arunachal Pradesh', '13' => 'Nagaland', '14' => 'Manipur',
            '15' => 'Mizoram', '16' => 'Tripura', '17' => 'Meghalaya', '18' => 'Assam',
            '19' => 'West Bengal', '20' => 'Jharkhand', '21' => 'Odisha', '22' => 'Chhattisgarh',
            '23' => 'Madhya Pradesh', '24' => 'Gujarat', '26' => 'Dadra and Nagar Haveli and Daman and Diu',
            '27' => 'Maharashtra', '29' => 'Karnataka', '30' => 'Goa', '32' => 'Kerala',
            '33' => 'Tamil Nadu', '34' => 'Puducherry', '36' => 'Telangana', '37' => 'Andhra Pradesh',
        ];

        return [
            'gstin' => $gst,
            'company_name' => '',
            'legal_name' => '',
            'business_type' => '',
            'taxpayer_type' => '',
            'status' => '',
            'registration_date' => '',
            'nature_of_business' => [],
            'primary_address' => '',
            'city' => '',
            'state' => $stateNames[$stateCode] ?? '',
            'state_code' => $stateCode,
            'country' => 'India',
            'last_updated' => '',
            'frequency' => '',
            'additional_addresses' => [],
            'pan' => $pan,
        ];
    }

    public function importVendors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        // Required headers mapping dynamically
        $headerMap = [];
        foreach ($header as $idx => $colName) {
            $cleanedColName = strtolower(trim($colName));
            $headerMap[$cleanedColName] = $idx;
        }
        
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json(['status' => false, 'error' => 'Unauthenticated'], 401);
        }
        
        $userBranchId = $authUser->id;
        if ($authUser->role == 'staff' && $authUser->branch_id) {
            $userBranchId = $authUser->branch_id;
        }

        $stateMap = [
            "Jammu and Kashmir" => "01", "Himachal Pradesh" => "02", "Punjab" => "03",
            "Chandigarh" => "04", "Uttarakhand" => "05", "Haryana" => "06", "Delhi" => "07",
            "Rajasthan" => "08", "Uttar Pradesh" => "09", "Bihar" => "10", "Sikkim" => "11",
            "Arunachal Pradesh" => "12", "Nagaland" => "13", "Manipur" => "14",
            "Mizoram" => "15", "Tripura" => "16", "Meghalaya" => "17", "Assam" => "18",
            "West Bengal" => "19", "Jharkhand" => "20", "Odisha" => "21", "Chhattisgarh" => "22",
            "Madhya Pradesh" => "23", "Gujarat" => "24", "Maharashtra" => "27",
            "Karnataka" => "29", "Tamil Nadu" => "33", "Telangana" => "36"
        ];

        $importedCount = 0;
        $invalidRows = [];

        foreach ($csvData as $index => $row) {
            if (count($row) < 2) continue; // Skip empty rows
            
            // Helper function to get value by header name safely
            $getVal = function($colName) use ($row, $headerMap) {
                $idx = $headerMap[strtolower($colName)] ?? -1;
                if ($idx !== -1 && isset($row[$idx])) {
                    return trim($row[$idx]);
                }
                return null;
            };

            $supplierName = $getVal('Supplier Name');
            $phone1 = $getVal('Phone #1') ?: ($getVal('Phone #2') ?: null);
            $email = $getVal('Email Id') ?? $getVal('EmailId') ?? $getVal('Email'); // Handle variations

            if (empty($supplierName)) {
                $invalidRows[] = "Row " . ($index + 2) . ": Supplier Name is required.";
                continue;
            }

            // Check if phone already exists (only if provided)
            if (!empty($phone1)) {
                $existingUser = User::where('phone', $phone1)
                    ->where('role', 'vendor')
                    ->where('branch_id', $userBranchId)
                    ->where('isDeleted', 0)
                    ->first();

                if ($existingUser) {
                    $invalidRows[] = "Row " . ($index + 2) . ": Phone number {$phone1} already exists.";
                    continue;
                }
            }
            
            if (!empty($email)) {
                $existingEmail = User::where('email', $email)
                    ->where('role', 'vendor')
                    ->where('branch_id', $userBranchId)
                    ->where('isDeleted', 0)
                    ->first();
                if ($existingEmail) {
                    $invalidRows[] = "Row " . ($index + 2) . ": Email {$email} already exists.";
                    continue;
                }
            }

            $stateName = $getVal('State');
            $stateCode = $stateMap[$stateName] ?? null;

            // Date parsing (Supplier Since) -> from dd/mm/yyyy to yyyy-mm-dd
            $supplierSinceRaw = $getVal('Supplier Since');
            $supplierSinceFormatted = null;
            if ($supplierSinceRaw) {
                // simple conversion if it's strictly dd/mm/yyyy or dd-mm-yyyy
                if (str_contains($supplierSinceRaw, '/')) {
                    $parts = explode('/', $supplierSinceRaw);
                    if (count($parts) == 3) {
                        $supplierSinceFormatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                    }
                } elseif (str_contains($supplierSinceRaw, '-')) {
                    $parts = explode('-', $supplierSinceRaw);
                    if (count($parts) == 3 && strlen($parts[0]) == 2) { // dd-mm-yyyy
                        $supplierSinceFormatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                    } else {
                        $supplierSinceFormatted = $supplierSinceRaw; // fallback assuming already yyyy-mm-dd
                    }
                }
            }

            $customer = new User();
            $customer->name = substr($supplierName, 0, 80);
            $customer->email = substr($email, 0, 100);
            $customer->phone = substr($phone1, 0, 15);
            $customer->state_code = substr((string)$stateCode, 0, 3);
            $customer->state_name = substr((string)$stateName, 0, 100);
            $customer->pan_number = substr((string)($getVal('PAN')), 0, 10);
            $customer->gst_number = substr((string)($getVal('GSTIN')), 0, 15);
            $customer->role = 'vendor';
            $customer->branch_id = $userBranchId;
            $customer->created_by = $authUser->id;
            
            $statusVal = strtolower($getVal('Status') ?: 'active');
            $customer->status = ($statusVal == 'inactive') ? 0 : 1;
            $customer->save();

            $customerDetail = new UserDetail();
            $customerDetail->user_id = $customer->id;
            $customerDetail->supplier_since = $supplierSinceFormatted;
            $customerDetail->address = substr((string)($getVal('Address Line1') ?? $getVal('Address Line 1')), 0, 500);
            $customerDetail->address_line2 = substr((string)($getVal('Address Line2') ?? $getVal('Address Line 2')), 0, 255);
            $customerDetail->address_line3 = substr((string)($getVal('Address Line3') ?? $getVal('Address Line 3')), 0, 255);
            $customerDetail->pin_code = substr((string)($getVal('Pin Code')), 0, 20);
            $customerDetail->phone_2 = substr((string)($getVal('Phone #2')), 0, 15);
            $customerDetail->city = substr((string)($getVal('City/Town')), 0, 100);
            $customerDetail->country = 'India'; // Default
            $customerDetail->supplier_category = substr((string)($getVal('Supplier Category')), 0, 100);
            $customerDetail->tin_number = substr((string)($getVal('TIN') ?? $getVal('TIN Number')), 0, 50);
            $customerDetail->credit_days = (int)($getVal('Credit Days') ?: 0);
            $customerDetail->pan_status = substr((string)($getVal('PAN Status')), 0, 50);
            $customerDetail->gstin_status = substr((string)($getVal('GSTIN Status')), 0, 50);
            $customerDetail->account_group = substr((string)($getVal('Account Group')), 0, 100);
            $customerDetail->tan_status = substr((string)($getVal('TAN Status')), 0, 50);
            $customerDetail->tan_number = substr((string)($getVal('TAN No.') ?? $getVal('TAN No')), 0, 50);
            $customerDetail->msme_status = substr((string)($getVal('MSME Status')), 0, 50);
            $customerDetail->msme_number = substr((string)($getVal('MSME No.') ?? $getVal('MSME No')), 0, 50);
            $customerDetail->status = $statusVal == 'inactive' ? 'inactive' : 'active';
            $customerDetail->save();

            $importedCount++;
        }

        $message = "Successfully imported {$importedCount} vendors.";

        if (count($invalidRows) > 0) {
            return response()->json([
                'status' => count($invalidRows) < count($csvData),
                'message' => $message,
                'invalid_skus' => $invalidRows
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
