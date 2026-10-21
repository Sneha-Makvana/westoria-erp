# Customer Filter - Quick Reference Guide

## What Was Added?

### New Feature
A **Customer Filter** dropdown on the Sales Pool Report page that allows users to:
1. Filter "Hot Selling Items" by specific customers
2. Filter "Hot Customers" by specific customers  
3. Filter summary statistics (Total Bills, Total Qty, Total Sales) by specific customers
4. Combine customer filter with existing Date Range and Staff filters

### How It Works

1. **Page Load:**
   - Staff dropdown populates with all staff members from the branch
   - Customer dropdown populates with all customers from the branch

2. **Applying Filters:**
   - Select desired Date Range, Staff (optional), Customer (optional)
   - Click "Apply Filters"
   - Tables and summary cards update instantly without page reload

3. **Available Options:**
   - "All Staff" (default) - Include all staff members
   - Specific staff member - Only items/customers from that staff
   - "All Customers" (default) - Include all customers
   - Specific customer - Only items/customers from that customer

## API Endpoints

### New Endpoints:
| Endpoint | Method | Returns | Purpose |
|----------|--------|---------|---------|
| `/sales-pool-report/customers` | GET | List of customers | Populate customer dropdown |
| `/sales-pool-report/staff-members` | GET | List of staff | Populate staff dropdown |

### Updated Endpoints:
| Endpoint | Method | New Parameters | Purpose |
|----------|--------|-----------------|---------|
| `/sales-pool-report/summary` | GET | `staff_id`, `customer_id` | Filter summary stats |
| `/sales-pool-report/hot-items` | GET | `staff_id`, `customer_id` | Filter hot selling items |
| `/sales-pool-report/hot-customers` | GET | `staff_id`, `customer_id` | Filter hot customers |

## Request Parameters

All endpoints support these query parameters:

```
from_date    : YYYY-MM-DD (required)
to_date      : YYYY-MM-DD (required)
staff_id     : integer or 0 for all (optional, default: 0)
customer_id  : integer or 0 for all (optional, default: 0)
```

### Example Request:
```
GET /sales-pool-report/hot-items?from_date=2026-07-01&to_date=2026-08-14&staff_id=5&customer_id=10
```

## Response Format

### Customer List Response:
```json
{
    "status": true,
    "data": [
        {"id": 1, "name": "Vadhare"},
        {"id": 2, "name": "New Customer"},
        {"id": 3, "name": "Sneha"}
    ]
}
```

### Staff List Response:
```json
{
    "status": true,
    "data": [
        {"id": 1, "name": "Admin User"},
        {"id": 5, "name": "LOKESH"},
        {"id": 8, "name": "John Doe"}
    ]
}
```

### Hot Items Response (with filters):
```json
{
    "status": true,
    "data": [
        {
            "rank": 1,
            "product_id": 123,
            "product_name": "Product Name",
            "category": "Electronics",
            "sku": "SKU123",
            "qty_sold": 10,
            "amount": 50000.00,
            "bills": 5
        }
    ],
    "currency_symbol": "₹",
    "currency_position": "left",
    "count": 1
}
```

## Filter Combinations

### Example: All combinations and their results

| Date Range | Staff | Customer | Result |
|-----------|-------|----------|--------|
| ✓ | ✗ | ✗ | Overall data for date range (default) |
| ✓ | ✓ | ✗ | Data from specific staff member only |
| ✓ | ✗ | ✓ | Data from specific customer only |
| ✓ | ✓ | ✓ | Data from staff to customer only |

Where:
- ✓ = Selected/Specified
- ✗ = "All" option selected (default)

## Files Modified

1. **Controller:** `app/Http/Controllers/admin/SalesPoolReportController.php`
   - Updated 3 existing methods (getHotSellingItems, getHotCustomers, getSummaryStats)
   - Added 2 new methods (getCustomers, getStaffMembers)

2. **View:** `resources/views/sales/salespool/index.blade.php`
   - Added Staff dropdown UI
   - Added Customer dropdown UI
   - Added CSS for select elements
   - Enhanced JavaScript with dropdown loading functions
   - Updated AJAX request parameters

3. **Routes:** `routes/web.php`
   - Added 2 new routes for dropdowns

## Database Queries

### Customers Query:
```sql
SELECT id, name FROM users 
WHERE branch_id = ? 
AND role = 'customer' 
AND isDeleted = 0 
ORDER BY name ASC
```

### Staff Members Query:
```sql
SELECT id, name FROM users 
WHERE branch_id = ? 
AND role IN ('admin', 'staff', 'sub-admin') 
AND isDeleted = 0 
ORDER BY name ASC
```

### Hot Items with Customer Filter:
```sql
SELECT oi.* FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.branch_id = ? 
AND o.quotation_status = 'sales' 
AND o.isDeleted = 0 
AND o.created_at BETWEEN ? AND ?
AND o.user_id = ? -- Customer filter
ORDER BY oi.quantity DESC
```

## Testing Checklist

- [ ] Page loads without errors
- [ ] Staff dropdown populates correctly
- [ ] Customer dropdown populates correctly
- [ ] Filters work individually (date, staff, customer)
- [ ] Filters work in combination
- [ ] "All Staff" and "All Customers" options work correctly
- [ ] Summary cards update when filters change
- [ ] Hot Items table updates when filters change
- [ ] Hot Customers table updates when filters change
- [ ] No data message displays when no results found
- [ ] Page responsive on mobile devices
- [ ] Consistent styling with rest of application

## Troubleshooting

### Dropdowns not populating:
- Check browser console for AJAX errors
- Verify routes are registered: `php artisan route:list | grep sales-pool-report`
- Check that users with roles 'customer', 'staff', 'admin' exist in database

### Filter not applying:
- Verify staff_id and customer_id are passed in AJAX request
- Check server logs for errors
- Ensure selected staff/customer have sales data in date range

### Performance issues:
- Verify database indexes on users.branch_id, orders.user_id, orders.staff_id
- Check that lazy loading isn't being used (eager loading implemented)

## Browser Compatibility

- Chrome/Edge: ✓ Fully supported
- Firefox: ✓ Fully supported
- Safari: ✓ Fully supported
- IE 11: ⚠ May have issues with modern JavaScript

## Notes

- Filters respect branch_id (multi-branch support)
- Filters work with role-based access control (staff only sees their data)
- No database migration required
- Backward compatible with existing code
- Filter values persist in dropdowns during session
