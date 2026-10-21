# Sales Pool Report - Implementation Guide

## Overview

The **Sales Pool Report** is a new comprehensive analytics feature within the Reports module that displays:
- **Top-Selling Items** (ranked by quantity sold)
- **Top Customers** (ranked by total amount spent)
- **Summary Statistics** (Total Bills, Total Qty, Total Sales, Top Customer)

The report uses date range filtering to provide flexible business intelligence capabilities similar to the Korvia ERP system.

---

## Files Created/Modified

### 1. **Controller** (`app/Http/Controllers/admin/SalesPoolReportController.php`)
   - **Methods:**
     - `index()` - Displays the main report page
     - `getHotSellingItems()` - AJAX endpoint for hot selling items data
     - `getHotCustomers()` - AJAX endpoint for top customers data
     - `getSummaryStats()` - AJAX endpoint for summary statistics
     - `resolveBranchId()` - Helper to resolve branch ID based on user role

   - **Key Features:**
     - Multi-branch support with proper scope handling
     - Staff/Admin/Sub-admin role-based access
     - Date range filtering with Carbon date validation
     - Efficient aggregation using Eloquent queries

### 2. **View** (`resources/views/sales/salespool/index.blade.php`)
   - **Components:**
     - Date range filter section
     - 4 summary statistic cards (Total Bills, Total Qty, Total Sales, Top Customer)
     - 2 data tabs (Hot Selling Items, Hot Customers)
     - Responsive data tables with ranking

   - **Features:**
     - Real-time AJAX data loading
     - Tab switching without page reload
     - Loading state indicators
     - Empty state messaging
     - Responsive design (mobile-friendly)
     - Currency formatting based on system settings

### 3. **Routes** (`routes/web.php`)
   ```php
   Route::get('/sales-pool-report', [SalesPoolReportController::class, 'index'])->name('sales-pool-report.index');
   Route::get('/sales-pool-report/summary', [SalesPoolReportController::class, 'getSummaryStats'])->name('sales-pool-report.summary');
   Route::get('/sales-pool-report/hot-items', [SalesPoolReportController::class, 'getHotSellingItems'])->name('sales-pool-report.items');
   Route::get('/sales-pool-report/hot-customers', [SalesPoolReportController::class, 'getHotCustomers'])->name('sales-pool-report.customers');
   ```

### 4. **Sidebar Menu** (`resources/views/layout/sidebar.blade.php`)
   - Added "Sales Pool Report" menu item under Reports section
   - Uses same permission ID (34) as Sales Report for authorization
   - Positioned after Sales Report for logical flow

---

## Features & Functionality

### 📊 Dashboard Components

#### **1. Filter Section**
- **From Date** - Start date for the report range
- **To Date** - End date for the report range
- **Apply Button** - Refreshes data based on selected dates
- Validates date input and shows error if dates are invalid

#### **2. Summary Cards**
- **Total Bills** - Number of confirmed sales orders
- **Total Qty Sold** - Sum of all quantities sold
- **Total Sales** - Total bill value in currency
- **Top Customer** - Name and amount spent by highest-spending customer

#### **3. Hot Selling Items Tab**
Shows a ranked table with columns:
- **Rank** - Sequential ranking
- **Product Name** - Name of the product
- **Category** - Product category
- **SKU** - Stock Keeping Unit
- **Qty Sold** - Total units sold
- **Amount** - Total revenue from product
- **Bills** - Number of bills item appeared in

*Sorted by quantity sold (descending)*

#### **4. Hot Customers Tab**
Shows a ranked table with columns:
- **Rank** - Sequential ranking
- **Customer Name** - Name of the customer
- **Total Bills** - Number of orders placed
- **Qty Purchased** - Total units purchased
- **Total Amount Spent** - Total amount spent (highlighted)
- **Top Purchased Item** - Most frequently purchased item by customer

*Sorted by total amount spent (descending)*

---

## Backend Implementation Details

### **Database Queries**

#### Hot Selling Items Query
```php
$query = OrderItem::with(['product.category'])
    ->whereHas('order', function ($q) use ($user, $branchId, $fromDate, $toDate) {
        $q->where('isDeleted', 0)
          ->where('branch_id', $branchId)
          ->where('quotation_status', 'sales') // Only confirmed sales
          ->whereBetween('created_at', [$fromDate, $toDate]);
        StaffDepartmentScope::applyOrderScope($q, $user);
    });
```

**Aggregation Logic:**
```php
$items = $query->get()->groupBy('product_id');

foreach ($items as $productId => $orderItems) {
    $totalQty = $orderItems->sum('quantity');
    $totalAmount = $orderItems->sum('total_amount');
    $billCount = $orderItems->pluck('order_id')->unique()->count();
}
```

#### Hot Customers Query
```php
$orders = Order::with(['user', 'orderItems.product'])
    ->where('isDeleted', 0)
    ->where('branch_id', $branchId)
    ->where('quotation_status', 'sales')
    ->whereBetween('created_at', [$fromDate, $toDate])
    ->get();

// Group by customer and aggregate
foreach ($orders->groupBy('user_id') as $customerId => $customerOrders) {
    $totalBills = count($customerOrders);
    $totalQtyPurchased = $customerOrders->flatMap(fn($o) => $o->orderItems)->sum('quantity');
    $totalAmountSpent = $customerOrders->sum('total_amount');
}
```

### **Multi-Branch & Role-Based Access**

The controller respects the StaffDepartmentScope service:

```php
private function resolveBranchId($user)
{
    if ($user->role === 'staff' && !empty($user->branch_id)) {
        return (int) $user->branch_id;  // Staff only sees their branch
    }

    if ($user->role === 'admin') {
        $selectedSubAdminId = session('selectedSubAdminId');
        if (!empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;  // Admin can select any branch
        }
    }

    return (int) $user->id;  // Default to user ID
}
```

---

## Frontend Implementation Details

### **AJAX Communication**

All data loading happens via AJAX with proper error handling:

```javascript
$.ajax({
    url: ROUTES.hotItems,  // Built from Laravel route helpers
    type: 'GET',
    data: {
        from_date: '2024-01-01',
        to_date: '2024-01-31'
    },
    success: function(response) {
        if (response.status) {
            // Populate table with response.data
        }
    },
    error: function(xhr) {
        // Show error or empty state
    }
});
```

### **Tab Management**

```javascript
$('.tab-button').click(function() {
    const tabName = $(this).data('tab');
    switchTab(tabName);  // Switch active tab
});

function switchTab(tabName) {
    $('.tab-button').removeClass('active');
    $('.tab-button[data-tab="' + tabName + '"]').addClass('active');
    
    $('.tab-panel').removeClass('active');
    $('#' + tabName).addClass('active');
}
```

### **Currency Formatting**

```javascript
function formatCurrency(amount) {
    const formatted = parseFloat(amount).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    if (currencyPosition === 'right') {
        return formatted + ' ' + currencySymbol;
    } else {
        return currencySymbol + ' ' + formatted;
    }
}
```

---

## Security & Permissions

### **Permission Checks**
- Uses `hasPermission()` helper with module ID **34** (Sales Report)
- Users must have "view" permission on Sales module
- Staff users are automatically scoped to their branch

### **Role-Based Access**
- **Admin**: Can view reports for all branches (via dropdown selector)
- **Sub-Admin**: Can view reports for their assigned branch only
- **Staff**: Can view reports for their assigned branch only

### **Data Isolation**
- `StaffDepartmentScope::applyOrderScope()` automatically filters orders
- No cross-branch data leakage possible
- Branch ID is resolved server-side, not from client input

---

## Usage Instructions

### **For End Users**

1. **Navigate to Report**
   - Go to Reports → Sales Pool Report from sidebar

2. **Select Date Range**
   - Choose "From Date" (start date)
   - Choose "To Date" (end date)
   - Click "Apply Filters"

3. **View Summary Statistics**
   - Top section shows key metrics
   - Updates automatically after filtering

4. **View Hot Selling Items**
   - First tab shows top products
   - Ranked by quantity sold
   - Shows amount and bill count for each product

5. **View Hot Customers**
   - Second tab shows top customers
   - Ranked by total amount spent
   - Shows top item purchased by each customer

---

## Customization Guide

### **Adding New Columns to Tables**

#### Example: Add "Profit Margin" to Hot Items table

1. **Backend** (SalesPoolReportController.php):
```php
// In getHotSellingItems() after calculating totalAmount:
$profitMargin = ($totalAmount / $totalCost) * 100;  // Calculate as needed

$hotSellingItems[] = [
    // ... existing fields
    'profit_margin' => round($profitMargin, 2),
];
```

2. **Frontend** (index.blade.php):
```php
// In table header:
<th style="text-align: right;">Profit Margin %</th>

// In JavaScript row building:
<td style="text-align: right;">${item.profit_margin}%</td>
```

### **Changing Sort Order**

In SalesPoolReportController, modify the sort:

```php
// Change from qty to amount:
usort($hotSellingItems, function ($a, $b) {
    return $b['amount'] <=> $a['amount'];  // Sort by amount instead
});
```

### **Adding Filters**

1. Add filter input in HTML
2. Pass to AJAX request
3. Apply WHERE clause in backend query

---

## Performance Considerations

### **Query Optimization**
- Uses eager loading (`with(['product.category', 'user'])`)
- Avoids N+1 queries
- Indexes on `created_at`, `branch_id`, `quotation_status`

### **Data Caching**
- Consider caching if report is accessed frequently:
```php
$key = "sales_pool_items_{$branchId}_{$fromDate}_{$toDate}";
return cache()->remember($key, 60, function () {
    // Generate report data
});
```

### **Pagination**
- If data exceeds 1000 rows, consider adding pagination
- Current implementation loads all data (suitable for typical use cases)

---

## Troubleshooting

### **No Data Showing**
1. Check date range - ensure orders exist within selected dates
2. Verify user has permission (ID 34)
3. Check branch ID is correct
4. Ensure orders have `quotation_status = 'sales'`

### **Currency Formatting Issues**
1. Verify currency settings in Settings module
2. Check `currencySymbol` and `currencyPosition` from API
3. Ensure locale is set to 'en-IN' for formatting

### **AJAX Errors**
1. Check browser console for errors
2. Verify route names match in JavaScript
3. Check Laravel error log for backend issues

---

## Testing Checklist

- [ ] Date range filtering works correctly
- [ ] Summary stats update when filters applied
- [ ] Hot items tab loads and sorts by quantity
- [ ] Hot customers tab loads and sorts by amount
- [ ] Tab switching works without page reload
- [ ] Empty state shows when no data
- [ ] Currency formatting matches system settings
- [ ] Staff user can only see own branch data
- [ ] Admin user can select different branches
- [ ] Loading indicators appear during data fetch
- [ ] No console errors in browser developer tools
- [ ] Responsive design works on mobile

---

## Related Files & References

- **Models Used**: Order, OrderItem, Product, User, Category, Setting
- **Services Used**: StaffDepartmentScope
- **Similar Reports**: SalesController::sales_report, orderReport
- **Permission Module**: Module ID 34 (Sales)
- **Routes File**: routes/web.php (lines 263-268)
- **Sidebar File**: resources/views/layout/sidebar.blade.php (lines 440-462)

---

## Future Enhancements

1. **PDF Export** - Generate PDF report with charts
2. **Excel Export** - Export data to Excel format
3. **Period Comparison** - Compare current period vs previous period
4. **Charts & Graphs** - Add visual representations (bar charts, pie charts)
5. **Advanced Filters** - Add category, customer type filters
6. **Scheduled Reports** - Send report via email on schedule
7. **Predictions** - ML-based predictions for next period sales
8. **Custom Date Ranges** - Preset ranges (Last 30 Days, This Month, etc.)

---

## Support & Maintenance

For issues or questions:
1. Check the **Troubleshooting** section above
2. Review the **Performance Considerations** for optimization tips
3. Refer to the **Implementation Details** for technical deep dives
4. Check similar report implementations in the codebase for patterns

---

**Last Updated**: August 14, 2024  
**Version**: 1.0  
**Status**: Production Ready
