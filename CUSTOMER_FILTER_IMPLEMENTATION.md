# Customer Filter Implementation for Sales Pool Report

## Summary
Added customer filtering functionality to the Sales Pool Report page, enabling users to filter top-selling items and summary statistics by customer, similar to the existing staff filtering capability. This allows for focused analysis of products sold to specific customers.

## Changes Made

### 1. Backend - Controller (SalesPoolReportController.php)

#### Updated Methods to Accept Filters:
- **getHotSellingItems()** - Added `staff_id` and `customer_id` parameters
  - When a customer is selected, only products purchased by that customer are shown
  - When staff is selected, only products sold by that staff are shown
  - Both filters can work independently or together
  
- **getHotCustomers()** - Added `staff_id` and `customer_id` parameters
  - Filters the hot customers list by staff and/or customer
  
- **getSummaryStats()** - Added `staff_id` and `customer_id` parameters
  - Updates Total Bills, Total Qty Sold, Total Sales, and Top Customer based on selected filters

#### New Methods:
- **getCustomers()** - Returns list of all customers for the branch
  - Returns: `[{id, name}, ...]`
  - Filters by branch_id, role='customer', isDeleted=0
  - Sorted alphabetically by name
  
- **getStaffMembers()** - Returns list of all staff members for the branch
  - Returns: `[{id, name}, ...]`
  - Filters by branch_id, role in ['admin', 'staff', 'sub-admin'], isDeleted=0
  - Sorted alphabetically by name

### 2. Frontend - View (resources/views/sales/salespool/index.blade.php)

#### New UI Elements:
- **Staff Dropdown** - Added select element with id="staffFilter"
  - Default option: "All Staff" (value="0")
  - Populated dynamically from getStaffMembers() endpoint
  
- **Customer Dropdown** - Added select element with id="customerFilter"
  - Default option: "All Customers" (value="0")
  - Populated dynamically from getCustomers() endpoint

#### Styling:
- Added CSS for select elements to match the existing filter styling
- Consistent with Date Range and Apply Filters button styling
- Focus states with orange accent color (#ff9f43)

#### JavaScript Enhancements:
- **loadStaffMembers()** - Fetches staff list on page load
  - Populates #staffFilter dropdown
  - AJAX call to `/sales-pool-report/staff-members`
  
- **loadCustomers()** - Fetches customer list on page load
  - Populates #customerFilter dropdown
  - AJAX call to `/sales-pool-report/customers`
  
- **getDateRange()** - Extended to include filter values
  - Returns: `{from_date, to_date, staff_id, customer_id}`
  
- **loadSummary()**, **loadHotSellingItems()**, **loadHotCustomers()** - Updated to pass filter parameters in AJAX requests

### 3. Routes (routes/web.php)

#### New Routes Added:
- `GET /sales-pool-report/customers` → `SalesPoolReportController@getCustomers` (name: 'sales-pool-report.get-customers')
- `GET /sales-pool-report/staff-members` → `SalesPoolReportController@getStaffMembers` (name: 'sales-pool-report.get-staff')

## Filtering Behavior

### Independent Filtering:
- **Date Range Only** - Shows overall top items/customers for the date range (default behavior)
- **Staff Only** - Shows items sold by selected staff member
- **Customer Only** - Shows items purchased by selected customer
- **Staff + Customer** - Shows items sold by staff to the selected customer
- **Date Range + Staff** - Shows items sold by staff in the date range
- **Date Range + Customer** - Shows items purchased by customer in the date range
- **All Combinations** - Shows items sold by staff to customer within the date range

### Summary Cards Update:
When filters are applied, all summary cards update to reflect the filtered data:
- **Total Bills** - Number of confirmed sales orders
- **Total Qty Sold** - Total quantity of items sold
- **Total Sales** - Total amount of sales
- **Top Customer** - The customer with highest spending (filtered results)

### Tables Update:
- **Hot Selling Items Tab** - Shows products ranked by quantity sold (filtered results)
- **Hot Customers Tab** - Shows customers ranked by total amount spent (filtered results)

## Query Logic

### Staff Filter Logic:
```php
if (!empty($staffId) && $staffId !== '0') {
    $query->where(function ($q) use ($staffId) {
        $q->where('created_by', $staffId)
          ->orWhere('staff_id', $staffId);
    });
}
```

### Customer Filter Logic:
```php
if (!empty($customerId) && $customerId !== '0') {
    $query->where('user_id', $customerId);
}
```

## Data Flow

1. **Page Load:**
   - Load staff members via AJAX → populate Staff dropdown
   - Load customers via AJAX → populate Customer dropdown
   - Load initial data (no filters)

2. **Apply Filters Click:**
   - Collect date range, staff_id, customer_id values
   - Make AJAX requests with all parameters
   - Update summary cards, Hot Items table, Hot Customers table
   - No page reload required

3. **Tab Switch:**
   - Switch between Hot Selling Items and Hot Customers views
   - Data already loaded from previous filter application

## Database Queries

### Customer List Query:
```php
User::where('branch_id', $branchId)
    ->where('role', 'customer')
    ->where('isDeleted', 0)
    ->orderBy('name', 'ASC')
    ->select('id', 'name')
    ->get()
```

### Staff List Query:
```php
User::where('branch_id', $branchId)
    ->whereIn('role', ['admin', 'staff', 'sub-admin'])
    ->where('isDeleted', 0)
    ->orderBy('name', 'ASC')
    ->select('id', 'name')
    ->get()
```

## Testing Recommendations

1. **Filter Combinations:**
   - Test each filter independently
   - Test all combinations (date + staff + customer)
   - Verify default "All" options work correctly

2. **Data Accuracy:**
   - Verify summary cards update correctly
   - Verify tables show correct data for selected filters
   - Verify ranks are calculated correctly

3. **Edge Cases:**
   - No data for selected filters
   - Single product/customer scenarios
   - Multiple staff members with same products sold

4. **UI/UX:**
   - Verify responsive layout on mobile
   - Check dropdown styling consistency
   - Verify loading states work properly

## Performance Notes

- Dropdowns are loaded on page load (one-time, no additional load with filtering)
- Filtering is done server-side with indexed queries
- No N+1 query issues due to eager loading of relationships
- Currency formatting handled client-side to reduce server load

## Backward Compatibility

- All changes are backward compatible
- Existing routes continue to work with or without filter parameters
- Default values (staff_id=0, customer_id=0) represent "All" selections
- No database migrations required
