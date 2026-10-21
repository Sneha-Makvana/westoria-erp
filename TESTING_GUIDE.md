# Sales Pool Report - Testing Guide

## Quick Start Testing

### 1. Verify Installation

#### Step 1: Check Files Exist
```bash
# Verify controller exists
ls app/Http/Controllers/admin/SalesPoolReportController.php

# Verify view exists
ls resources/views/sales/salespool/index.blade.php
```

#### Step 2: Check Routes
```bash
php artisan route:list | grep sales-pool
```

Expected output:
```
GET|HEAD  sales-pool-report              sales-pool-report.index
GET|HEAD  sales-pool-report/summary      sales-pool-report.summary
GET|HEAD  sales-pool-report/hot-items    sales-pool-report.items
GET|HEAD  sales-pool-report/hot-customers sales-pool-report.customers
```

#### Step 3: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

---

## Manual Testing Procedures

### Test 1: Menu Item Appears

**Steps:**
1. Log in to the application as an admin user
2. Look at the left sidebar
3. Expand "Reports" section

**Expected Result:**
- "Sales Pool Report" appears in the menu after "Sales Report"
- Menu item is visible and clickable

**If Failed:**
- Check sidebar.blade.php modification
- Verify permission ID 34 is set correctly
- Check user has Sales Report permission

---

### Test 2: Page Loads

**Steps:**
1. Click on "Sales Pool Report" in sidebar
2. Wait for page to load
3. Observe the page

**Expected Result:**
- Page loads without errors
- Sees "Sales Pool Report" heading
- Sees filter section with date inputs
- Sees 4 summary cards
- Sees 2 tabs (Hot Selling Items, Hot Customers)
- Default dates shown (last 30 days)

**If Failed:**
- Check browser console (F12) for errors
- Check Laravel log file: `storage/logs/laravel.log`
- Verify route exists: `route('sales-pool-report.index')`
- Verify view file exists

---

### Test 3: Date Filter Works

**Steps:**
1. Open the report
2. Change "From Date" to a date with orders (e.g., 2024-01-01)
3. Change "To Date" to a date with orders (e.g., 2024-12-31)
4. Click "Apply Filters"
5. Wait 2-3 seconds

**Expected Result:**
- Loading spinner appears briefly
- Summary cards populate with numbers
- Hot Selling Items table shows products
- Hot Customers table shows customers

**If Failed:**
- Check browser console for AJAX errors
- Verify routes are working: `curl http://localhost/sales-pool-report/summary?from_date=2024-01-01&to_date=2024-12-31`
- Check Laravel error log
- Verify orders exist in database with `quotation_status = 'sales'`

---

### Test 4: Summary Statistics

**Steps:**
1. Apply filters with a date range
2. Observe the 4 summary cards

**Expected Results:**

| Card | Expected Value |
|------|-----------------|
| Total Bills | Number of orders in date range |
| Total Qty Sold | Sum of all quantities in those orders |
| Total Sales | Sum of all order amounts |
| Top Customer | Name of highest-spending customer |

**If Numbers Seem Wrong:**
- Check if date range includes actual orders
- Verify orders are marked as "sales" (not quotations)
- Check currency settings match database values

---

### Test 5: Hot Selling Items Tab

**Steps:**
1. Apply filters with data
2. Observe "Hot Selling Items" tab (should be active)
3. Review the table

**Expected Results:**
- Table shows products ranked by quantity sold
- Columns visible: Rank, Product Name, Category, SKU, Qty Sold, Amount, Bills
- Products sorted highest qty to lowest
- Amounts formatted as currency

**Verify Calculation:**
- Pick product #1
- Qty Sold should match: SUM(order_item.quantity) where product_id = X
- Amount should match: SUM(order_item.total_amount) where product_id = X
- Bills should match: COUNT(DISTINCT order_id) where product_id = X

**If Failed:**
- Check browser console for AJAX errors
- Verify order items have correct product_id
- Check amounts are calculated in order_item.total_amount

---

### Test 6: Hot Customers Tab

**Steps:**
1. Click on "Hot Customers" tab
2. Review the table

**Expected Results:**
- Table shows customers ranked by total amount spent
- Columns visible: Rank, Customer Name, Total Bills, Qty Purchased, Amount Spent, Top Item
- Customers sorted highest amount to lowest
- Amounts formatted as currency
- Top item shows most frequently purchased item

**Verify Calculation:**
- Pick customer #1
- Total Bills should match: COUNT(DISTINCT order_id) where user_id = X
- Qty Purchased should match: SUM(order_item.quantity) where order has user_id = X
- Amount Spent should match: SUM(order.total_amount) where user_id = X

**If Failed:**
- Check browser console for AJAX errors
- Verify orders have correct user_id
- Check customer names populate correctly

---

### Test 7: Tab Switching (No Reload)

**Steps:**
1. On "Hot Selling Items" tab, right-click → Inspect (Open DevTools)
2. Go to Network tab
3. Click "Hot Customers" tab
4. Observe network requests

**Expected Result:**
- One AJAX request to `/sales-pool-report/hot-customers`
- NO page refresh/reload
- Tab content switches without page reload

**If Failed:**
- Check if full page reload happens (wrong!)
- Check Network tab for AJAX request
- Verify JavaScript click handler working

---

### Test 8: Currency Formatting

**Steps:**
1. Apply filters
2. Check any currency amount displayed

**Expected Result:**
- Currency symbol appears (₹ or other)
- Numbers formatted with commas (e.g., 1,00,000)
- Decimal places shown (e.g., 1,00,000.00)

**To Verify Correct Position:**
- Go to Settings module
- Check currency symbol
- Check currency position (left/right)
- Return to report
- Verify amounts match the position setting

**If Formatting Wrong:**
- Check Settings → Currency settings
- Verify `currencySymbol` and `currencyPosition` in AJAX response
- Check browser console for `formatCurrency()` function output

---

### Test 9: Empty State Handling

**Steps:**
1. Set date range to future dates (e.g., 2099-01-01 to 2099-12-31)
2. Click "Apply Filters"

**Expected Result:**
- Summary cards show 0
- Hot Selling Items shows empty state message: "No selling data available for the selected date range."
- Hot Customers shows empty state message: "No customer data available for the selected date range."

**If Failed:**
- Empty state HTML should be displayed, not errors
- Check that no JavaScript errors occur

---

### Test 10: Error Handling

**Steps:**
1. Leave "From Date" empty
2. Click "Apply Filters"

**Expected Result:**
- Browser alert shows: "Please select both From Date and To Date."
- Data does not load

**Alternative Test:**
1. Set "From Date" to invalid format (e.g., "abc")
2. System should either:
   - Show validation error, or
   - Revert to default date

---

### Test 11: Multi-Branch Access (Admin)

**Steps:**
1. Log in as Admin user
2. Access Sales Pool Report
3. Check if date shows orders from current selected branch
4. Change branch in sidebar branch selector (if available)
5. Return to report
6. Check if data changes

**Expected Result:**
- Report data changes based on selected branch
- Data only shows orders from selected branch

**If Failed:**
- Check `resolveBranchId()` method in controller
- Verify branch scope is applied to queries
- Check session('selectedSubAdminId') is set correctly

---

### Test 12: Multi-Branch Access (Staff)

**Steps:**
1. Log in as Staff user
2. Access Sales Pool Report
3. Check data shown

**Expected Result:**
- Staff sees only their branch's data
- Cannot see other branches' data
- No branch selector available

**If Failed:**
- Check that StaffDepartmentScope is applied
- Verify `user->branch_id` is set for staff

---

### Test 13: Responsive Design - Mobile

**Steps:**
1. Open report on desktop
2. Open DevTools (F12)
3. Toggle device toolbar (mobile view)
4. Set to iPhone 12 (390px wide)
5. Reload page

**Expected Result:**
- Layout stacks vertically
- Date inputs stack on top of each other
- Summary cards stack in single column
- Table is readable on mobile
- No horizontal scrolling needed

**If Failed:**
- CSS media queries may need adjustment
- Check styles for `max-width: 768px`

---

### Test 14: Browser Compatibility

**Test in Each Browser:**
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

**Expected Result:**
- Report works in all browsers
- Styling looks consistent
- No JavaScript errors
- Date picker works

---

### Test 15: Performance Test

**Steps:**
1. Open DevTools (F12)
2. Go to Performance tab
3. Open Sales Pool Report
4. Click "Record"
5. Apply filters
6. Stop recording
7. Analyze results

**Expected Result:**
- Initial page load < 2 seconds
- Filter application < 1 second
- No layout thrashing
- No jank/stuttering

**If Slow:**
- Check Network tab for slow requests
- Verify database queries are optimized
- Check if too much data is being loaded

---

## Database Testing

### Verify Data Integrity

```sql
-- Count orders in date range
SELECT COUNT(*) as total_bills
FROM orders
WHERE branch_id = [YOUR_BRANCH_ID]
  AND quotation_status = 'sales'
  AND isDeleted = 0
  AND created_at BETWEEN '2024-01-01' AND '2024-12-31';

-- Sum quantities
SELECT SUM(quantity) as total_qty
FROM order_items
JOIN orders ON order_items.order_id = orders.id
WHERE orders.branch_id = [YOUR_BRANCH_ID]
  AND orders.quotation_status = 'sales'
  AND orders.isDeleted = 0
  AND orders.created_at BETWEEN '2024-01-01' AND '2024-12-31';

-- Sum amounts
SELECT SUM(total_amount) as total_sales
FROM orders
WHERE branch_id = [YOUR_BRANCH_ID]
  AND quotation_status = 'sales'
  AND isDeleted = 0
  AND created_at BETWEEN '2024-01-01' AND '2024-12-31';

-- Top customer
SELECT user_id, SUM(total_amount) as total_spent
FROM orders
WHERE branch_id = [YOUR_BRANCH_ID]
  AND quotation_status = 'sales'
  AND isDeleted = 0
  AND created_at BETWEEN '2024-01-01' AND '2024-12-31'
GROUP BY user_id
ORDER BY total_spent DESC
LIMIT 1;
```

Compare results with report data.

---

## Debugging Tips

### Enable Debug Mode

Edit `.env`:
```
APP_DEBUG=true
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

### Use Browser DevTools

```javascript
// In browser console, to see AJAX requests:
// 1. Go to Network tab
// 2. Filter by XHR
// 3. Apply filters in report
// 4. Click on request to see details
```

### Test API Endpoints Directly

```bash
curl -H "Accept: application/json" \
     "http://localhost/sales-pool-report/summary?from_date=2024-01-01&to_date=2024-12-31" \
     -b "LARAVEL_SESSION=your_session_cookie"
```

---

## Regression Testing

### After Report Works, Test These Scenarios

1. **Other Reports Still Work**
   - [ ] Sales Report still works
   - [ ] Purchase Report still works
   - [ ] Expense Report still works
   - [ ] P&L Statement still works

2. **Sidebar Still Works**
   - [ ] Menu expands/collapses
   - [ ] Other menu items clickable
   - [ ] No styling broken

3. **No Performance Regression**
   - [ ] Page load times acceptable
   - [ ] Database not overloaded
   - [ ] No memory leaks

---

## Test Coverage Checklist

| Feature | Tested | Result | Notes |
|---------|--------|--------|-------|
| Menu item visible | [ ] | ✓/✗ | |
| Page loads | [ ] | ✓/✗ | |
| Date filtering works | [ ] | ✓/✗ | |
| Summary stats calculate | [ ] | ✓/✗ | |
| Hot items table loads | [ ] | ✓/✗ | |
| Hot customers table loads | [ ] | ✓/✗ | |
| Tab switching works | [ ] | ✓/✗ | |
| Currency formatting correct | [ ] | ✓/✗ | |
| Empty state shows | [ ] | ✓/✗ | |
| Error handling works | [ ] | ✓/✗ | |
| Admin multi-branch works | [ ] | ✓/✗ | |
| Staff branch isolation works | [ ] | ✓/✗ | |
| Responsive mobile design | [ ] | ✓/✗ | |
| Browser compatibility | [ ] | ✓/✗ | |
| Performance acceptable | [ ] | ✓/✗ | |

---

## Sign-Off

**Tested By**: __________________ **Date**: __________

**Test Status**: 
- [ ] All tests passed - Ready for production
- [ ] Some tests failed - See notes
- [ ] Critical issues found - Do not deploy

**Notes/Issues Found**:
```
[List any issues found during testing]
```

---

**Testing Complete**: Yes / No  
**Ready for Production**: Yes / No
