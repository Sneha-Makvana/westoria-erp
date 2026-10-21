# Add Sale Page - Field Visibility Implementation Guide

## Quick Reference

This guide shows how to implement conditional field visibility on the Add Sale page based on the Sales Settings.

## Step-by-Step Implementation

### Step 1: Pass Settings to View

In your **SalesController** (or wherever the Add Sale page is rendered), pass the settings:

```php
// In app/Http/Controllers/admin/SalesController.php (or similar)

public function add_sales(Request $request)
{
    $user = auth()->user();
    $branchId = $user->branch_id ?? $user->id;
    
    // Get settings for the branch
    $setting = Setting::where('branch_id', $branchId)->first();
    
    // Pass settings to view
    return view('sales.add-sales', compact('setting'));
    // or return view('sales.pos', compact('setting')); if POS is the add sale page
}
```

### Step 2: Modify Blade Template

In your **Add Sale blade file** (`resources/views/sales/add-sales.blade.php` or `resources/views/sales/pos.blade.php`):

#### Example 1: Assign Staff Field
```blade
@if($setting && $setting->show_assign_staff)
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Assign Staff</label>
            <select name="assigned_staff_id" id="assigned_staff_id" class="form-select">
                <option value="">Select Staff</option>
                @foreach($staffList as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
```

#### Example 2: Order Type Field
```blade
@if($setting && $setting->show_order_type)
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Order Type</label>
            <select name="order_type" id="order_type" class="form-select">
                <option value="">Choose Order Type</option>
                <option value="delivery">Delivery</option>
                <option value="pickup">Pickup</option>
                <option value="dine-in">Dine In</option>
            </select>
        </div>
    </div>
@endif
```

#### Example 3: Advance Receipt
```blade
@if($setting && $setting->advance_receipt)
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Advance Receipt</label>
            <select name="is_advance_receipt" id="is_advance_receipt" class="form-select">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
@endif
```

#### Example 4: Rental
```blade
@if($setting && $setting->rental)
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Is Rental?</label>
            <select name="is_rental" id="is_rental" class="form-select">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
@endif
```

#### Example 5: TDS Apply
```blade
@if($setting && $setting->tds_apply)
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Apply TDS</label>
            <select name="apply_tds" id="apply_tds" class="form-select">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
@endif
```

### Step 3: JavaScript Handling (Optional)

If you're using AJAX to load settings dynamically:

```javascript
// Load settings via API
function loadSalesSettings() {
    $.ajax({
        url: "/api/general-settings",
        type: "GET",
        headers: {
            "Authorization": "Bearer " + authToken
        },
        success: function(response) {
            const settings = response.settings;
            
            // Show/hide fields based on settings
            if (settings.show_assign_staff) {
                $("#assign_staff_wrapper").show();
            } else {
                $("#assign_staff_wrapper").hide();
            }
            
            if (settings.show_order_type) {
                $("#order_type_wrapper").show();
            } else {
                $("#order_type_wrapper").hide();
            }
            
            // And so on for other fields...
        }
    });
}

$(document).ready(function() {
    loadSalesSettings();
});
```

### Step 4: Complete Example - Full Add Sale Form Section

```blade
<div class="row">
    <!-- Customer Field (Always visible) -->
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Customer <span class="text-danger">*</span></label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Conditional: Assign Staff -->
    @if($setting && $setting->show_assign_staff)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Assign Staff</label>
                <select name="assigned_staff_id" id="assigned_staff_id" class="form-select">
                    <option value="">Select Staff</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    
    <!-- Conditional: Order Type -->
    @if($setting && $setting->show_order_type)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Order Type</label>
                <select name="order_type" id="order_type" class="form-select">
                    <option value="">Select Type</option>
                    <option value="delivery">Delivery</option>
                    <option value="pickup">Pickup</option>
                </select>
            </div>
        </div>
    @endif
    
    <!-- Date Field (Always visible) -->
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="form-group">
            <label>Date <span class="text-danger">*</span></label>
            <input type="date" name="sale_date" id="sale_date" class="form-control" required>
        </div>
    </div>
</div>

<div class="row">
    <!-- Conditional: Advance Receipt -->
    @if($setting && $setting->advance_receipt)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Advance Receipt</label>
                <select name="is_advance_receipt" id="is_advance_receipt" class="form-select">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    @endif
    
    <!-- Conditional: Rental -->
    @if($setting && $setting->rental)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Is Rental?</label>
                <select name="is_rental" id="is_rental" class="form-select">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    @endif
    
    <!-- Conditional: TDS Apply -->
    @if($setting && $setting->tds_apply)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="form-group">
                <label>Apply TDS</label>
                <select name="apply_tds" id="apply_tds" class="form-select">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    @endif
</div>
```

## Testing Your Implementation

1. **Test with all settings ON:**
   - Go to Settings > Sales Setting
   - Turn all toggles to "On"
   - Save
   - Go to Add Sale page
   - All fields should be visible

2. **Test with all settings OFF:**
   - Go to Settings > Sales Setting
   - Turn all toggles to "Off"
   - Save
   - Go to Add Sale page
   - Optional fields should be hidden

3. **Test mixed settings:**
   - Enable some, disable others
   - Verify only enabled fields appear

## Common Issues & Solutions

### Issue 1: Fields not showing/hiding
**Solution:** Make sure you're passing `$setting` to the view from controller.

### Issue 2: Settings always null
**Solution:** Verify the branch_id is correct and settings exist in database for that branch.

### Issue 3: Changes not reflecting
**Solution:** Clear browser cache and Laravel cache:
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Pro Tips

1. **Use Default Values:** Always provide default behavior when settings are null:
   ```blade
   @if(!$setting || $setting->show_assign_staff)
       <!-- Show by default if setting is not configured -->
   @endif
   ```

2. **Group Related Fields:** Keep related fields together visually

3. **Responsive Design:** Test on mobile - hidden fields should not break layout

4. **Form Validation:** Update validation rules based on visible fields

5. **API Integration:** If using AJAX submissions, exclude hidden field values

---

**Quick Copy-Paste Checklist:**
- [ ] Added `$setting` variable in controller
- [ ] Wrapped fields with `@if($setting && $setting->field_name)` conditions
- [ ] Tested with settings ON
- [ ] Tested with settings OFF
- [ ] Cleared caches
- [ ] Tested on mobile devices

