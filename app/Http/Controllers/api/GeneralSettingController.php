<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{

    public function show(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;
        $BranchId     = $user->branch_id;

        $selectedSubAdminId = $request->query('selectedSubAdminId');

        // JS localStorage returns the string "null" when no value is set — treat it as empty
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }
        // Common: fetch settings (filter by branch if needed)
        $settings = Setting::where('branch_id', $selectedSubAdminId)->first();

        return response()->json([
            'status'   => true,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;

        // JS localStorage returns the string "null" when no value is set — treat it as empty
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        // Sanitize string 'undefined' or 'null' values sent from JS formData
        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                $inputs[$key] = null;
            }
        }
        $request->merge($inputs);

        $request->validate([
            'shop_name'         => 'required|string',
            'gst_num'           => 'nullable',
            'cin_no'            => 'nullable|string',
            'low_stock'         => 'nullable',
            'state_code'        => 'nullable',
            'email'             => 'required|email',
            'phone'             => 'required',
            'address'           => 'required|string',
            'terms_condition_eng' => 'nullable|string',
            'terms_condition_guj' => 'nullable|string',
            'currency_symbol'   => 'required|string',
            'currency_position' => 'required|string',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'favicon'           => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'qr_code'           => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'bank_name' => 'required|string',
            'branch' => 'required|string',
            'ac_no' => 'required|string',
            'ifsc_code' => 'required|string',
            'invoice_size'                       => 'nullable|in:small,big',
            'send_mail'                          => 'nullable|boolean',
            'financial_year'                     => 'nullable|boolean',
            'tds_apply'                          => 'nullable|boolean',
            'show_crm_dashboard'                 => 'nullable|boolean',
            'show_hr_dashboard'                  => 'nullable|boolean',
            'customer_whatsapp_message'          => 'nullable|boolean',
            'admin_whatsapp_message'             => 'nullable|boolean',
            'appointment_reminder_hours_before'  => 'nullable|integer|min:1',
            'office_latitude'                    => 'nullable|numeric',
            'office_longitude'                   => 'nullable|numeric',
            'office_radius'                      => 'nullable|integer|min:0',
            'advance_receipt'                    => 'nullable|boolean',
            'rental'                             => 'nullable|boolean',
            'enable_variant_product'             => 'nullable|boolean',
        ]);

        // Fetch or create branch-specific settings
        $settings = Setting::where('branch_id', $selectedSubAdminId)->first();

        if (! $settings) {
            $settings            = new Setting();
            $settings->branch_id = $selectedSubAdminId; // assign branch
        }

        $settings->gst_num           = $request->gst_num;
        $settings->cin_no            = $request->cin_no;
        $settings->low_stock         = $request->low_stock;
        $settings->name              = $request->shop_name;
        $settings->email             = $request->email;
        $settings->phone             = $request->phone;
        $settings->state_code        = $request->state_code;
        $settings->address           = $request->address;
        $settings->terms_condition_eng = $request->terms_condition_eng;
        $settings->terms_condition_guj = $request->terms_condition_guj;
        $settings->currency_position = $request->currency_position;
        $settings->currency_symbol   = $request->currency_symbol;
        $settings->bank_name = $request->bank_name;
        $settings->branch = $request->branch;
        $settings->ac_no = $request->ac_no;
        $settings->ifsc_code = $request->ifsc_code;
        $settings->invoice_size      = $request->invoice_size;
        $settings->send_mail     = $request->has('send_mail')
            ? (int) $request->send_mail
            : ($settings->send_mail ?? 1);
        $settings->financial_year = $request->has('financial_year')
            ? (int) $request->financial_year
            : ($settings->financial_year ?? 1);
        $settings->tds_apply = $request->has('tds_apply')
            ? (int) $request->tds_apply
            : ($settings->tds_apply ?? 1);
        $settings->show_crm_dashboard = $request->has('show_crm_dashboard')
            ? (int) $request->show_crm_dashboard
            : ($settings->show_crm_dashboard ?? 1);
        $settings->show_hr_dashboard = $request->has('show_hr_dashboard')
            ? (int) $request->show_hr_dashboard
            : ($settings->show_hr_dashboard ?? 1);

        $settings->customer_whatsapp_message         = $request->has('customer_whatsapp_message')
            ? (int) $request->customer_whatsapp_message
            : ($settings->customer_whatsapp_message ?? 1);
        $settings->admin_whatsapp_message            = $request->has('admin_whatsapp_message')
            ? (int) $request->admin_whatsapp_message
            : ($settings->admin_whatsapp_message ?? 1);
        $settings->advance_receipt                   = $request->has('advance_receipt')
            ? (int) $request->advance_receipt
            : ($settings->advance_receipt ?? 0);
        $settings->rental                            = $request->has('rental')
            ? (int) $request->rental
            : ($settings->rental ?? 0);
        $settings->enable_variant_product            = $request->has('enable_variant_product')
            ? (int) $request->enable_variant_product
            : ($settings->enable_variant_product ?? 1);
        $settings->appointment_reminder_hours_before = $request->appointment_reminder_hours_before ?? ($settings->appointment_reminder_hours_before ?? 3);

        // GPS / Location
        $settings->office_latitude  = $request->office_latitude  ?: $settings->office_latitude;
        $settings->office_longitude = $request->office_longitude ?: $settings->office_longitude;
        $settings->office_radius    = $request->has('office_radius') ? (int) $request->office_radius : ($settings->office_radius ?? 200);

        if ($request->hasFile('logo')) {
            $logoPath       = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            $faviconPath       = $request->file('favicon')->store('favicons', 'public');
            $settings->favicon = $faviconPath;
        }

        if ($request->hasFile('qr_code')) {
            $qr_code           = $request->file('qr_code')->store('qr_codes', 'public');
            $settings->qr_code = $qr_code;
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateDeliveryTerms(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;

        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $validator = Validator::make($request->all(), [
            'terms_condition_eng' => 'nullable|string',
            'terms_condition_guj' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        $settings->terms_condition_eng = $request->terms_condition_eng;
        $settings->terms_condition_guj = $request->terms_condition_guj;
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Delivery terms & conditions updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateCompanyRules(Request $request)
    {
        $user               = Auth::guard('api')->user();
        $role               = $user->role;
        $userBranchId       = $user->id;
        $selectedSubAdminId = $request->selectedSubAdminId;

        // 🔹 Role-based branch selection
        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'working_hours'        => 'required',
            'sunday_off'           => 'required|in:yes,no',
            'saturday_off'         => 'required|in:yes,no',
            'grace_period'         => 'required',
            'lunch_break'          => 'nullable',
            'open_time'            => 'required|date_format:H:i',
            'close_time'           => 'required|date_format:H:i',
            'overtime_after_hours' => 'nullable|numeric|min:0',
            'tax_deduction_amount' => 'required|numeric|min:0',
            'salary_exceeds_amount'=> 'required|numeric|min:0',
            'location_check_enabled' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // 🔹 Fetch Setting for Branch
        $settings = Setting::where('branch_id', $selectedSubAdminId)->first();

        if (! $settings) {
            return response()->json([
                'status'  => false,
                'message' => 'Settings not found for this branch',
            ], 404);
        }

        // 🔹 Update Company Rules
        $settings->update([
            'working_hours'          => $request->working_hours,
            'sunday_off'             => $request->sunday_off,
            'saturday_off'           => $request->saturday_off,
            'grace_period'           => $request->grace_period,
            'lunch_break'            => $request->lunch_break,
            'open_time'              => $request->open_time,
            'close_time'             => $request->close_time,
            'overtime_after_hours'   => $request->overtime_after_hours ?: null,
            'tax_deduction_amount'   => $request->tax_deduction_amount,
            'salary_exceeds_amount'  => $request->salary_exceeds_amount,
            'location_check_enabled' => (int) ($request->location_check_enabled ?? 0),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Company rules updated successfully',
            'data'    => $settings,
        ]);
    }

    public function updateDashboardSettings(Request $request)
    {
        $user               = Auth::guard('api')->user();
        $role               = $user->role;
        $userBranchId       = $user->id;
        $selectedSubAdminId = $request->selectedSubAdminId;

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        // Sanitize string 'undefined' or 'null' values sent from JS formData
        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                $inputs[$key] = null;
            }
        }
        $request->merge($inputs);

        $validator = Validator::make($request->all(), [
            'show_crm_dashboard'         => 'nullable|boolean',
            'show_hr_dashboard'          => 'nullable|boolean',
            'show_erp_dashboard'         => 'nullable|boolean',
            'show_crm_lead_pipeline'     => 'nullable|boolean',
            'show_crm_conversion'        => 'nullable|boolean',
            'show_crm_followup_load'     => 'nullable|boolean',
            'show_crm_meeting_momentum'  => 'nullable|boolean',
            'show_crm_lead_status_mix'   => 'nullable|boolean',
            'show_crm_activity_trend'    => 'nullable|boolean',
            'show_crm_pipeline_quality'  => 'nullable|boolean',
            'show_crm_recent_leads'      => 'nullable|boolean',
            'show_crm_next_7_days'       => 'nullable|boolean',
            'show_hr_staff_strength'     => 'nullable|boolean',
            'show_hr_active_staff'       => 'nullable|boolean',
            'show_hr_monthly_attendance' => 'nullable|boolean',
            'show_hr_personal_progress'  => 'nullable|boolean',
            'show_hr_attendance_pattern' => 'nullable|boolean',
            'show_hr_salary_payroll_trend' => 'nullable|boolean',
            'show_hr_payroll_snapshot'   => 'nullable|boolean',
            'show_hr_attendance_watch'   => 'nullable|boolean',
            'show_hr_payroll_status'     => 'nullable|boolean',
            'show_erp_total_sales'       => 'nullable|boolean',
            'show_erp_total_purchase'    => 'nullable|boolean',
            'show_erp_total_expense'     => 'nullable|boolean',
            'show_erp_sales_invoice_count' => 'nullable|boolean',
            'show_erp_purchase_invoice_count' => 'nullable|boolean',
            'show_erp_customers_count'   => 'nullable|boolean',
            'show_erp_vendors_count'     => 'nullable|boolean',
            'show_erp_sales_chart'       => 'nullable|boolean',
            'show_erp_purchase_chart'    => 'nullable|boolean',
            'show_erp_recent_sales'      => 'nullable|boolean',
            'show_erp_recent_purchases'  => 'nullable|boolean',
            'show_erp_recent_products'   => 'nullable|boolean',
            'show_erp_products_delivery' => 'nullable|boolean',
            'show_erp_recent_rentals'    => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        
        // Main CRM dashboard toggle
        if ($request->has('show_crm_dashboard')) {
            $settings->show_crm_dashboard = (int) $request->show_crm_dashboard;
        }
        
        // Main HR dashboard toggle
        if ($request->has('show_hr_dashboard')) {
            $settings->show_hr_dashboard = (int) $request->show_hr_dashboard;
        }
        
        // Main ERP dashboard toggle
        if ($request->has('show_erp_dashboard')) {
            $settings->show_erp_dashboard = (int) $request->show_erp_dashboard;
        }
        
        // CRM subsection toggles
        if ($request->has('show_crm_lead_pipeline')) {
            $settings->show_crm_lead_pipeline = (int) $request->show_crm_lead_pipeline;
        }
        if ($request->has('show_crm_conversion')) {
            $settings->show_crm_conversion = (int) $request->show_crm_conversion;
        }
        if ($request->has('show_crm_followup_load')) {
            $settings->show_crm_followup_load = (int) $request->show_crm_followup_load;
        }
        if ($request->has('show_crm_meeting_momentum')) {
            $settings->show_crm_meeting_momentum = (int) $request->show_crm_meeting_momentum;
        }
        if ($request->has('show_crm_lead_status_mix')) {
            $settings->show_crm_lead_status_mix = (int) $request->show_crm_lead_status_mix;
        }
        if ($request->has('show_crm_activity_trend')) {
            $settings->show_crm_activity_trend = (int) $request->show_crm_activity_trend;
        }
        if ($request->has('show_crm_pipeline_quality')) {
            $settings->show_crm_pipeline_quality = (int) $request->show_crm_pipeline_quality;
        }
        if ($request->has('show_crm_recent_leads')) {
            $settings->show_crm_recent_leads = (int) $request->show_crm_recent_leads;
        }
        if ($request->has('show_crm_next_7_days')) {
            $settings->show_crm_next_7_days = (int) $request->show_crm_next_7_days;
        }
        
        // HR subsection toggles
        if ($request->has('show_hr_staff_strength')) {
            $settings->show_hr_staff_strength = (int) $request->show_hr_staff_strength;
        }
        if ($request->has('show_hr_active_staff')) {
            $settings->show_hr_active_staff = (int) $request->show_hr_active_staff;
        }
        if ($request->has('show_hr_monthly_attendance')) {
            $settings->show_hr_monthly_attendance = (int) $request->show_hr_monthly_attendance;
        }
        if ($request->has('show_hr_personal_progress')) {
            $settings->show_hr_personal_progress = (int) $request->show_hr_personal_progress;
        }
        if ($request->has('show_hr_attendance_pattern')) {
            $settings->show_hr_attendance_pattern = (int) $request->show_hr_attendance_pattern;
        }
        if ($request->has('show_hr_salary_payroll_trend')) {
            $settings->show_hr_salary_payroll_trend = (int) $request->show_hr_salary_payroll_trend;
        }
        if ($request->has('show_hr_payroll_snapshot')) {
            $settings->show_hr_payroll_snapshot = (int) $request->show_hr_payroll_snapshot;
        }
        if ($request->has('show_hr_attendance_watch')) {
            $settings->show_hr_attendance_watch = (int) $request->show_hr_attendance_watch;
        }
        if ($request->has('show_hr_payroll_status')) {
            $settings->show_hr_payroll_status = (int) $request->show_hr_payroll_status;
        }
        
        // ERP subsection toggles
        if ($request->has('show_erp_total_sales')) {
            $settings->show_erp_total_sales = (int) $request->show_erp_total_sales;
        }
        if ($request->has('show_erp_total_purchase')) {
            $settings->show_erp_total_purchase = (int) $request->show_erp_total_purchase;
        }
        if ($request->has('show_erp_total_expense')) {
            $settings->show_erp_total_expense = (int) $request->show_erp_total_expense;
        }
        if ($request->has('show_erp_sales_invoice_count')) {
            $settings->show_erp_sales_invoice_count = (int) $request->show_erp_sales_invoice_count;
        }
        if ($request->has('show_erp_purchase_invoice_count')) {
            $settings->show_erp_purchase_invoice_count = (int) $request->show_erp_purchase_invoice_count;
        }
        if ($request->has('show_erp_customers_count')) {
            $settings->show_erp_customers_count = (int) $request->show_erp_customers_count;
        }
        if ($request->has('show_erp_vendors_count')) {
            $settings->show_erp_vendors_count = (int) $request->show_erp_vendors_count;
        }
        if ($request->has('show_erp_sales_chart')) {
            $settings->show_erp_sales_chart = (int) $request->show_erp_sales_chart;
        }
        if ($request->has('show_erp_purchase_chart')) {
            $settings->show_erp_purchase_chart = (int) $request->show_erp_purchase_chart;
        }
        if ($request->has('show_erp_recent_sales')) {
            $settings->show_erp_recent_sales = (int) $request->show_erp_recent_sales;
        }
        if ($request->has('show_erp_recent_purchases')) {
            $settings->show_erp_recent_purchases = (int) $request->show_erp_recent_purchases;
        }
        if ($request->has('show_erp_recent_products')) {
            $settings->show_erp_recent_products = (int) $request->show_erp_recent_products;
        }
        if ($request->has('show_erp_products_delivery')) {
            $settings->show_erp_products_delivery = (int) $request->show_erp_products_delivery;
        }
        if ($request->has('show_erp_recent_rentals')) {
            $settings->show_erp_recent_rentals = (int) $request->show_erp_recent_rentals;
        }
        
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Dashboard settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateEwayBillSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        
        $settings->enable_eway_bill = $request->has('enable_eway_bill') ? (int) $request->enable_eway_bill : 0;
        $settings->eway_gstin_number = $request->eway_gstin_number;
        $settings->eway_legal_business_name = $request->eway_legal_business_name;
        $settings->eway_pan_number = $request->eway_pan_number;
        $settings->eway_state = $request->eway_state;
        $settings->eway_state_code = $request->eway_state_code;
        $settings->eway_address_line_1 = $request->eway_address_line_1;
        $settings->eway_address_line_2 = $request->eway_address_line_2;
        $settings->eway_city = $request->eway_city;
        $settings->eway_pin_code = $request->eway_pin_code;
        $settings->eway_api_username = $request->eway_api_username;
        if ($request->filled('eway_api_password')) {
            $settings->eway_api_password = $request->eway_api_password;
        }
        $settings->eway_app_key_client_id = $request->eway_app_key_client_id;
        
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'E-Way Bill settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateEinvoiceSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        
        $settings->enable_einvoice = $request->has('enable_einvoice') ? (int) $request->enable_einvoice : 0;
        $settings->einvoice_api_username = $request->einvoice_api_username;
        if ($request->filled('einvoice_api_password')) {
            $settings->einvoice_api_password = $request->einvoice_api_password;
        }
        $settings->einvoice_client_id = $request->einvoice_client_id;
        $settings->einvoice_client_secret = $request->einvoice_client_secret;
        $settings->einvoice_api_endpoint_url = $request->einvoice_api_endpoint_url;
        $settings->einvoice_gstin = $request->einvoice_gstin;
        $settings->einvoice_nic_public_key = $request->einvoice_nic_public_key;
        
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'E-Invoice settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateSalesSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null) {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && ! empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $validator = Validator::make($request->all(), [
            'advance_receipt'   => 'nullable|boolean',
            'rental'            => 'nullable|boolean',
            'financial_year'    => 'nullable|boolean',
            'tds_apply'         => 'nullable|boolean',
            'show_assign_staff' => 'nullable|boolean',
            'show_order_type'   => 'nullable|boolean',
            'barcode_type'      => 'nullable|in:barcode,qrcode',
            'barcode_setting_type' => 'nullable|in:simple,detailed',
            'barcode_detail_option' => 'nullable|in:long,short',
            'payment_methods'   => 'nullable|array',
            'payment_methods.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);

        if ($request->has('advance_receipt')) {
            $settings->advance_receipt = (int) $request->advance_receipt;
        }
        if ($request->has('rental')) {
            $settings->rental = (int) $request->rental;
        }
        if ($request->has('financial_year')) {
            $settings->financial_year = (int) $request->financial_year;
        }
        if ($request->has('tds_apply')) {
            $settings->tds_apply = (int) $request->tds_apply;
        }
        if ($request->has('show_assign_staff')) {
            $settings->show_assign_staff = (int) $request->show_assign_staff;
        }
        if ($request->has('show_order_type')) {
            $settings->show_order_type = (int) $request->show_order_type;
        }
        if ($request->has('barcode_type')) {
            $settings->barcode_type = $request->barcode_type;
        }
        if ($request->has('barcode_setting_type')) {
            $settings->barcode_setting_type = $request->barcode_setting_type;
            if ($request->barcode_setting_type === 'simple') {
                $settings->barcode_detail_option = null;
            }
        }
        if ($request->has('barcode_detail_option') && $request->barcode_setting_type === 'detailed') {
            $settings->barcode_detail_option = $request->barcode_detail_option;
        }
        if ($request->has('payment_methods')) {
            $settings->payment_methods = $request->payment_methods;
        } elseif ($request->hasAny(['advance_receipt', 'rental', 'financial_year', 'tds_apply', 'show_assign_staff', 'show_order_type'])) {
            $settings->payment_methods = [];
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Sales settings updated successfully',
            'settings' => $settings,
        ]);
    }
}
