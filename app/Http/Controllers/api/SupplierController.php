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
        $gst = $request->input('gst_number');

        if (!$gst) {
            return response()->json(['error' => 'GST number is required'], 400);
        }

        // Example API: Replace URL with your actual API provider
        $apiUrl = "https://cleartax.in/f/compliance-report/{$gst}";
        // $apiKey = "YOUR_API_KEY_HERE";

        $response = Http::withHeaders([
            // 'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($apiUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'API request failed'], 500);
        }

        $data = $response->json();

        if (!isset($data['taxpayerInfo'])) {
            return response()->json(['error' => 'Invalid GST or no data found'], 404);
        }

        $info = $data['taxpayerInfo'];

        // Company / Business Details
        $companyName = $info['tradeNam'] ?? ($info['lgnm'] ?? '');
        $gstin = $info['gstin'] ?? $gst;
        $legalName = $info['lgnm'] ?? '';
        $businessType = $info['ctb'] ?? '';
        $status = $info['sts'] ?? '';
        $registrationDate = $info['rgdt'] ?? '';
        $taxpayerType = $info['dty'] ?? '';
        $natureOfBusiness = $info['nba'] ?? []; // array of business types
        $lastUpdated = $info['lstupdt'] ?? '';
        $frequency = $info['frequencyType'] ?? '';
        $pradr = $info['pradr'] ?? [];

        // Primary Address
        $primaryAddr = $pradr['addr'] ?? [];
        $address = trim(
            ($primaryAddr['bnm'] ?? '') . ', ' .
            ($primaryAddr['st'] ?? '') . ', ' .
            ($primaryAddr['loc'] ?? '') . ', ' .
            ($primaryAddr['dst'] ?? '') . ', ' .
            ($primaryAddr['stcd'] ?? '') . ' - ' .
            ($primaryAddr['pncd'] ?? ''),
            ', -'
        );

        // Additional Addresses
        $additionalAddresses = [];
        if (isset($info['adadr']) && is_array($info['adadr'])) {
            foreach ($info['adadr'] as $addrItem) {
                $addr = $addrItem['addr'] ?? [];
                $additionalAddresses[] = [
                    'name' => $addr['bnm'] ?? '',
                    'address' => trim(
                        ($addr['bnm'] ?? '') . ', ' .
                        ($addr['st'] ?? '') . ', ' .
                        ($addr['loc'] ?? '') . ', ' .
                        ($addr['dst'] ?? '') . ', ' .
                        ($addr['stcd'] ?? '') . ' - ' .
                        ($addr['pncd'] ?? ''),
                        ', -'
                    ),
                    'city' => $addr['dst'] ?? '',
                    'state' => $addr['stcd'] ?? '',
                    'nature' => $addrItem['ntr'] ?? '',
                ];
            }
        }

        $country = $primaryAddr['country'] ?? ($info['country'] ?? 'India');

        return response()->json([
            'gstin' => $gstin,
            'company_name' => $companyName,
            'legal_name' => $legalName,
            'business_type' => $businessType,
            'taxpayer_type' => $taxpayerType,
            'status' => $status,
            'registration_date' => $registrationDate,
            'nature_of_business' => $natureOfBusiness,
            'primary_address' => $address,
            'city' => $primaryAddr['dst'] ?? '',
            'state' => $primaryAddr['stcd'] ?? '',
            'country' => $country,
            'last_updated' => $lastUpdated,
            'frequency' => $frequency,
            'additional_addresses' => $additionalAddresses,
        ]);
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
