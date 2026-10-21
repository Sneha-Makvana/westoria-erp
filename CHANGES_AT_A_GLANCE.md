# Customer Filter - Changes At A Glance

## What Users See

### Before:
```
┌─────────────────────────────────────────────────────┐
│  Date Range    │  [From Date] │ [To Date] │ Apply   │
│                                                     │
│  [Filter Results Based on Date Range Only]          │
└─────────────────────────────────────────────────────┘
```

### After:
```
┌────────────────────────────────────────────────────────────────┐
│  Date Range    │  [From Date] │ [To Date]  │ Apply Filters    │
│  Staff         │  [All Staff ▼]                                │
│  Customer      │  [All Customers ▼]                           │
│                                                                │
│  [Filter Results Based on Date + Staff + Customer]            │
└────────────────────────────────────────────────────────────────┘
```

## What Changed in Code

### 1. Controller Method Signatures

#### Before:
```php
public function getHotSellingItems(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    // ... only date filtering
}
```

#### After:
```php
public function getHotSellingItems(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $staffId = $request->input('staff_id');        // NEW
    $customerId = $request->input('customer_id');  // NEW
    
    // ... date + staff + customer filtering
}
```

### 2. Query Logic

#### Before:
```php
$query = OrderItem::with(['product.category'])
    ->whereHas('order', function ($q) use ($user, $branchId, $fromDate, $toDate) {
        $q->where('isDeleted', 0)
          ->where('branch_id', $branchId)
          ->where('quotation_status', 'sales')
          ->whereBetween('created_at', [$fromDate, $toDate]);
        
        StaffDepartmentScope::applyOrderScope($q, $user);
    });
```

#### After:
```php
$query = OrderItem::with(['product.category'])
    ->whereHas('order', function ($q) use (..., $staffId, $customerId) {
        // ... existing filters
        
        // NEW: Apply staff filter if provided
        if (!empty($staffId) && $staffId !== '0') {
            $q->where(function ($staffQuery) use ($staffId) {
                $staffQuery->where('created_by', $staffId)
                           ->orWhere('staff_id', $staffId);
            });
        }
        
        // NEW: Apply customer filter if provided
        if (!empty($customerId) && $customerId !== '0') {
            $q->where('user_id', $customerId);
        }
    });
```

### 3. HTML UI

#### Before:
```html
<div class="filter-section">
    <div class="filter-row">
        <div class="filter-group">
            <label for="fromDate">From Date</label>
            <input type="date" id="fromDate" name="from_date">
        </div>
        <div class="filter-group">
            <label for="toDate">To Date</label>
            <input type="date" id="toDate" name="to_date">
        </div>
        <div class="filter-group" style="flex: 0 0 auto;">
            <button class="btn-apply" id="applyFilters">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>
    </div>
</div>
```

#### After:
```html
<div class="filter-section">
    <div class="filter-row">
        <div class="filter-group">
            <label for="fromDate">From Date</label>
            <input type="date" id="fromDate" name="from_date">
        </div>
        <div class="filter-group">
            <label for="toDate">To Date</label>
            <input type="date" id="toDate" name="to_date">
        </div>
        <!-- NEW: Staff Filter -->
        <div class="filter-group">
            <label for="staffFilter">Staff</label>
            <select id="staffFilter" name="staff_id">
                <option value="0">All Staff</option>
            </select>
        </div>
        <!-- NEW: Customer Filter -->
        <div class="filter-group">
            <label for="customerFilter">Customer</label>
            <select id="customerFilter" name="customer_id">
                <option value="0">All Customers</option>
            </select>
        </div>
        <div class="filter-group" style="flex: 0 0 auto;">
            <button class="btn-apply" id="applyFilters">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>
    </div>
</div>
```

### 4. JavaScript Routes

#### Before:
```javascript
const ROUTES = {
    summary: '{{ route("sales-pool-report.summary") }}',
    hotItems: '{{ route("sales-pool-report.items") }}',
    hotCustomers: '{{ route("sales-pool-report.customers") }}'
};
```

#### After:
```javascript
const ROUTES = {
    summary: '{{ route("sales-pool-report.summary") }}',
    hotItems: '{{ route("sales-pool-report.items") }}',
    hotCustomers: '{{ route("sales-pool-report.customers") }}',
    customers: '{{ route("sales-pool-report.get-customers") }}',        // NEW
    staffMembers: '{{ route("sales-pool-report.get-staff") }}'           // NEW
};
```

### 5. JavaScript Initialization

#### Before:
```javascript
$(document).ready(function() {
    // Load initial data
    loadAllData();
    
    // Tab switching
    $('.tab-button').click(function() {
        const tabName = $(this).data('tab');
        switchTab(tabName);
    });
    
    // Apply filters
    $('#applyFilters').click(function() {
        loadAllData();
    });
});
```

#### After:
```javascript
$(document).ready(function() {
    // NEW: Load dropdown data
    loadStaffMembers();
    loadCustomers();
    
    // Load initial data
    loadAllData();
    
    // Tab switching
    $('.tab-button').click(function() {
        const tabName = $(this).data('tab');
        switchTab(tabName);
    });
    
    // Apply filters
    $('#applyFilters').click(function() {
        loadAllData();
    });
});
```

### 6. JavaScript Data Collection

#### Before:
```javascript
function getDateRange() {
    const fromDate = $('#fromDate').val();
    const toDate = $('#toDate').val();
    
    if (!fromDate || !toDate) {
        alert('Please select both From Date and To Date.');
        return null;
    }
    
    return {
        from_date: fromDate,
        to_date: toDate
    };
}
```

#### After:
```javascript
function getDateRange() {
    const fromDate = $('#fromDate').val();
    const toDate = $('#toDate').val();
    
    if (!fromDate || !toDate) {
        alert('Please select both From Date and To Date.');
        return null;
    }
    
    return {
        from_date: fromDate,
        to_date: toDate,
        staff_id: $('#staffFilter').val() || '0',        // NEW
        customer_id: $('#customerFilter').val() || '0'   // NEW
    };
}
```

### 7. AJAX Requests

#### Before:
```javascript
$.ajax({
    url: ROUTES.hotItems,
    type: 'GET',
    data: {
        from_date: fromDate,
        to_date: toDate
    },
    success: function(response) {
        // ... handle response
    }
});
```

#### After:
```javascript
$.ajax({
    url: ROUTES.hotItems,
    type: 'GET',
    data: {
        from_date: fromDate,
        to_date: toDate,
        staff_id: staffId,        // NEW
        customer_id: customerId   // NEW
    },
    success: function(response) {
        // ... handle response
    }
});
```

### 8. New Routes

#### Added to routes/web.php:
```php
// NEW: Get customer list for dropdown
Route::get('/sales-pool-report/customers', 
    [SalesPoolReportController::class, 'getCustomers'])
    ->name('sales-pool-report.get-customers');

// NEW: Get staff members list for dropdown
Route::get('/sales-pool-report/staff-members', 
    [SalesPoolReportController::class, 'getStaffMembers'])
    ->name('sales-pool-report.get-staff');
```

## API Request Examples

### Get Customers
```
GET /sales-pool-report/customers
Response: {
    "status": true,
    "data": [
        {"id": 1, "name": "Vadhare"},
        {"id": 2, "name": "New Customer"}
    ]
}
```

### Get Staff Members
```
GET /sales-pool-report/staff-members
Response: {
    "status": true,
    "data": [
        {"id": 1, "name": "Admin"},
        {"id": 5, "name": "Lokesh"}
    ]
}
```

### Get Hot Items with Customer Filter
```
GET /sales-pool-report/hot-items?from_date=2026-07-01&to_date=2026-08-14&customer_id=1
Response: {
    "status": true,
    "data": [
        {
            "rank": 1,
            "product_name": "Product A",
            "qty_sold": 10,
            "amount": 5000,
            ...
        }
    ]
}
```

## Summary of Changes

| What | Before | After | Lines Added |
|-----|--------|-------|------------|
| Controller Methods | 3 | 5 (+2 new) | ~150 |
| Routes | 4 | 6 (+2 new) | 2 |
| UI Elements | 2 inputs | 2 inputs + 2 selects | ~50 |
| JavaScript Functions | 8 | 10 (+2 new) | ~200 |
| **Total** | | | **~400** |

## Key Points

✅ **Non-Breaking** - Old requests without filters still work  
✅ **Backward Compatible** - Defaults to "All" (value=0)  
✅ **Efficient** - Server-side filtering, no client-side data processing  
✅ **Scalable** - Works with large numbers of customers/staff  
✅ **Responsive** - UI adjusts to different screen sizes  
✅ **Accessible** - Follows HTML standards, proper labels  
✅ **Tested** - All syntax validated, routes verified  
