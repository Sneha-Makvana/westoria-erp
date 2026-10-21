# Sales Settings Tab Implementation Guide

## Overview
This implementation adds a new **"Sales Setting"** tab to the Shop & Company Settings page that controls the visibility of fields on the Add Sale page.

## What Was Changed

### 1. Database Changes
**File:** `database/migrations/2026_07_21_000001_add_sales_settings_to_settings_table.php`

Added two new boolean fields to the `settings` table:
- `show_assign_staff` (default: true) - Controls "Assign Staff" field visibility on Add Sale
- `show_order_type` (default: true) - Controls "Order Type" field visibility on Add Sale

**To Run Migration:**
```bash
php artisan migrate
```

### 2. Model Updates
**File:** `app/Models/Setting.php`

Added to `$fillable` array:
- `show_assign_staff`
- `show_order_type`

Added to `$casts` array:
- `'show_assign_staff' => 'boolean'`
- `'show_order_type' => 'boolean'`

### 3. API Route
**File:** `routes/api.php`

Added new route:
```php
Route::post('/sales-settings/update', [GeneralSettingController::class, 'updateSalesSettings'])->name('sales-settings.update');
```

### 4. Controller Method
**File:** `app/Http/Controllers/api/GeneralSettingController.php`

Added new method `updateSalesSettings()` that:
- Validates the incoming request
- Handles admin/staff/branch-specific settings
- Updates these 6 fields:
  - `advance_receipt`
  - `rental`
  - `financial_year`
  - `tds_apply`
  - `show_assign_staff` (NEW)
  - `show_order_type` (NEW)

### 5. View Updates
**File:** `resources/views/settings/generalsettings.blade.php`

#### Changes Made:
1. **Added new tab button** in the navigation (after E-Invoice Settings tab)
   ```html
   <li class="nav-item" role="presentation">
       <button class="nav-link" id="sales-tab" ...>
           💰 Sales Setting
       </button>
   </li>
   ```

2. **Moved fields from "System Preferences" (Step 3) to new "Sales Setting" tab:**
   - Advance Receipt
   - Rental
   - Financial Year
   - TDS Apply

3. **Added new tab content section** with all 6 sales-related settings:
   - Advance Receipt (On/Off)
   - Rental (On/Off)
   - Financial Year (On/Off)
   - TDS Apply (On/Off)
   - Assign Staff (On/Off) ← NEW
   - Order Type (On/Off) ← NEW

4. **JavaScript Updates:**
   - Added loading logic in `loadGeneralSettings()` for `show_assign_staff` and `show_order_type`
   - Added `#saveSalesSettings` click handler that:
     - Collects all 6 field values
     - Posts to `/api/sales-settings/update`
     - Shows success/error message
     - Reloads page on success

## How to Use

### For Administrators:
1. Go to **Settings > Shop & Company Settings**
2. Click on the **"💰 Sales Setting"** tab
3. Toggle the following options:
   - **Advance Receipt**: Show/hide advance receipt option on Add Sale
   - **Rental**: Show/hide rental option on Add Sale
   - **Financial Year**: Enable/disable financial year filter on sales list
   - **TDS Apply**: Show/hide TDS Apply field on Add Sale
   - **Assign Staff**: Show/hide "Assign Staff" dropdown on Add Sale page
   - **Order Type**: Show/hide "Order Type" field on Add Sale page
4. Click **"Save Sales Settings"** button
5. The page will reload and changes will take effect immediately

### For Developers - Implementing Field Visibility on Add Sale Page:

You need to modify the Add Sale page (`resources/views/sales/pos.blade.php` or similar) to check these settings and show/hide fields accordingly.

**Example implementation:**

```php
// In your controller (e.g., SalesController::add_sales or pos method)
$setting = Setting::where('branch_id', $branchId)->first();

return view('sales.add-sales', compact('setting'));
```

```blade
<!-- In your add-sales blade file -->
@if($setting && $setting->show_assign_staff)
    <div class="form-group">
        <label>Assign Staff</label>
        <select name="assigned_staff" id="assigned_staff" class="form-select">
            <option value="">Select Staff</option>
            <!-- Staff options here -->
        </select>
    </div>
@endif

@if($setting && $setting->show_order_type)
    <div class="form-group">
        <label>Order Type</label>
        <select name="order_type" id="order_type" class="form-select">
            <option value="">Select Order Type</option>
            <option value="delivery">Delivery</option>
            <option value="pickup">Pickup</option>
            <!-- Other order types -->
        </select>
    </div>
@endif

@if($setting && $setting->advance_receipt)
    <!-- Show advance receipt fields -->
@endif

@if($setting && $setting->rental)
    <!-- Show rental fields -->
@endif

@if($setting && $setting->tds_apply)
    <!-- Show TDS fields -->
@endif
```

## API Endpoints

### Get Settings
```http
GET /api/general-settings
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "settings": {
        "advance_receipt": 0,
        "rental": 0,
        "financial_year": 1,
        "tds_apply": 1,
        "show_assign_staff": 1,
        "show_order_type": 1,
        // ... other settings
    }
}
```

### Update Sales Settings
```http
POST /api/sales-settings/update
Authorization: Bearer {token}
Content-Type: application/json

{
    "advance_receipt": 1,
    "rental": 0,
    "financial_year": 1,
    "tds_apply": 1,
    "show_assign_staff": 0,
    "show_order_type": 1,
    "selectedSubAdminId": 123 // Optional, for admin managing branches
}
```

**Response:**
```json
{
    "status": true,
    "message": "Sales settings updated successfully",
    "settings": { /* updated settings object */ }
}
```

## Testing Checklist

- [ ] Run database migration successfully
- [ ] Navigate to Settings page and see new "Sales Setting" tab
- [ ] Click on "Sales Setting" tab - should show 6 toggle fields
- [ ] Toggle each setting and save - should show success message
- [ ] Refresh page - settings should persist
- [ ] Test with different user roles (admin, staff, sub-admin)
- [ ] Verify settings are branch-specific (if multi-branch)
- [ ] Implement field visibility on Add Sale page based on settings
- [ ] Test Add Sale page - fields should show/hide based on settings

## Notes

- All settings default to **On (1)** for `show_assign_staff` and `show_order_type`
- The actual implementation of conditional field rendering on the Add Sale page must be done separately
- Settings are branch-specific - each branch can have different configurations
- The JavaScript uses jQuery and SweetAlert2 for UI interactions

## File Changes Summary

### Created Files:
1. `database/migrations/2026_07_21_000001_add_sales_settings_to_settings_table.php`

### Modified Files:
1. `app/Models/Setting.php` - Added new fields to fillable and casts
2. `routes/api.php` - Added sales-settings route
3. `app/Http/Controllers/api/GeneralSettingController.php` - Added updateSalesSettings method
4. `resources/views/settings/generalsettings.blade.php` - Added tab UI and JavaScript

## Next Steps

To complete the implementation:

1. **Run the migration** (when database is ready):
   ```bash
   php artisan migrate
   ```

2. **Modify the Add Sale page** to conditionally show fields based on settings

3. **Test thoroughly** in your development environment

4. **Deploy to production** following your deployment process

---

**Implementation Date:** July 21, 2026
**Version:** 1.0
