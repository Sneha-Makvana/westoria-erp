# Customer Filter Feature - Architecture Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     SALES POOL REPORT PAGE                      │
│                                                                 │
│  ┌──────────────────── UI LAYER ────────────────────────────┐  │
│  │                                                            │  │
│  │  Filter Row:                                              │  │
│  │  ├─ [From Date] ──► Input                                │  │
│  │  ├─ [To Date] ──► Input                                  │  │
│  │  ├─ [Staff ▼] ──► SELECT (Populated)                     │  │
│  │  ├─ [Customer ▼] ──► SELECT (Populated)                  │  │
│  │  └─ [Apply Filters] ──► BUTTON                           │  │
│  │                                                            │  │
│  │  Summary Cards:                                           │  │
│  │  ├─ Total Bills                                          │  │
│  │  ├─ Total Qty Sold                                       │  │
│  │  ├─ Total Sales                                          │  │
│  │  └─ Top Customer                                         │  │
│  │                                                            │  │
│  │  Tabs:                                                    │  │
│  │  ├─ Hot Selling Items (Table)                            │  │
│  │  └─ Hot Customers (Table)                                │  │
│  │                                                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                            ▼ AJAX                               │
│  ┌──────────────────── JS LAYER ────────────────────────────┐  │
│  │                                                            │  │
│  │  Event Handlers:                                          │  │
│  │  ├─ loadStaffMembers() ──► Get dropdown data             │  │
│  │  ├─ loadCustomers() ──► Get dropdown data                │  │
│  │  ├─ loadAllData() ──► Trigger all 3 loads               │  │
│  │  ├─ loadSummary() ──► Fetch summary stats                │  │
│  │  ├─ loadHotSellingItems() ──► Fetch items               │  │
│  │  ├─ loadHotCustomers() ──► Fetch customers              │  │
│  │  └─ switchTab() ──► Handle tab clicks                    │  │
│  │                                                            │  │
│  │  Data Collection:                                         │  │
│  │  getDateRange() → {from_date, to_date, staff_id, ...}   │  │
│  │                                                            │  │
│  └────────────────────────────────────────────────────────────┘  │
│                            ▼ HTTP                               │
└─────────────────────────────────────────────────────────────────┘
                                ▼
                        ┌────────────────┐
                        │ WEB SERVER     │
                        │ (Laravel)      │
                        └────────────────┘
                                ▼
          ┌─────────────────────────────────────────┐
          │   SalesPoolReportController (API)       │
          │                                         │
          │  GET /sales-pool-report                 │
          │  ├─ index() → returns view              │
          │  │                                     │
          │  GET /sales-pool-report/summary         │
          │  ├─ getSummaryStats()                   │
          │  │   ├─ Apply date filter              │
          │  │   ├─ Apply staff filter (if set)    │
          │  │   ├─ Apply customer filter (if set)  │
          │  │   └─ Return JSON                     │
          │  │                                     │
          │  GET /sales-pool-report/hot-items       │
          │  ├─ getHotSellingItems()                │
          │  │   ├─ Apply date filter              │
          │  │   ├─ Apply staff filter (if set)    │
          │  │   ├─ Apply customer filter (if set)  │
          │  │   └─ Return JSON (ranked products)   │
          │  │                                     │
          │  GET /sales-pool-report/hot-customers   │
          │  ├─ getHotCustomers()                   │
          │  │   ├─ Apply date filter              │
          │  │   ├─ Apply staff filter (if set)    │
          │  │   ├─ Apply customer filter (if set)  │
          │  │   └─ Return JSON (ranked customers)  │
          │  │                                     │
          │  GET /sales-pool-report/customers       │
          │  ├─ getCustomers() ── NEW              │
          │  │   └─ Return customer list JSON      │
          │  │                                     │
          │  GET /sales-pool-report/staff-members   │
          │  └─ getStaffMembers() ── NEW           │
          │      └─ Return staff list JSON          │
          │                                         │
          └─────────────────────────────────────────┘
                                ▼
          ┌─────────────────────────────────────────┐
          │        DATABASE QUERIES                 │
          │                                         │
          │  Users Table:                           │
          │  └─ GET customers/staff filtered by     │
          │     branch_id & role                    │
          │                                         │
          │  Orders Table:                          │
          │  └─ GET filtered by:                    │
          │     ├─ branch_id                        │
          │     ├─ quotation_status='sales'         │
          │     ├─ date range                       │
          │     ├─ staff_id (if filter set)         │
          │     └─ user_id (if filter set)          │
          │                                         │
          │  OrderItems Table:                      │
          │  └─ GET related to filtered orders      │
          │     ├─ Grouped by product_id            │
          │     ├─ Grouped by customer              │
          │     └─ Aggregated by qty/amount         │
          │                                         │
          └─────────────────────────────────────────┘
```

## Data Flow Diagram

### Initial Load
```
User Loads Page
       ▼
loadStaffMembers()
   ▼         ▼
AJAX   Receives JSON
   ▼
<select id="staffFilter">
  <option value="0">All Staff</option>
  <option value="1">Staff 1</option>
  ...
</select>

(Same for loadCustomers())

loadAllData() 
   ▼
3 parallel AJAX requests:
├─ getSummaryStats()
├─ getHotSellingItems()  
└─ getHotCustomers()
   ▼
Populate Summary Cards & Tables
```

### Filter Application
```
User Selects Filters:
├─ Date Range: 2026-07-01 to 2026-08-14
├─ Staff: 5 (Lokesh)
└─ Customer: 10 (Vadhare)

Click "Apply Filters"
       ▼
getDateRange() collects:
{
  from_date: "2026-07-01",
  to_date: "2026-08-14",
  staff_id: "5",
  customer_id: "10"
}
       ▼
loadAllData() with params
       ▼
3 parallel AJAX calls with params:
├─ /sales-pool-report/summary?...
├─ /sales-pool-report/hot-items?...
└─ /sales-pool-report/hot-customers?...
       ▼
Server applies filters in queries
       ▼
Return filtered JSON results
       ▼
JS renders tables & cards
with filtered data
       ▼
No page reload ✓
```

## Database Query Flow

### Hot Selling Items with Filters

```
Input Parameters:
├─ from_date: 2026-07-01
├─ to_date: 2026-08-14
├─ staff_id: 5
└─ customer_id: 10

Query Builder:
┌────────────────────────────────────┐
│ SELECT oi.* FROM order_items oi    │
│ WHERE EXISTS (                     │
│   SELECT 1 FROM orders o           │
│   WHERE o.id = oi.order_id         │
│   AND o.isDeleted = 0              │ ← Base filter
│   AND o.branch_id = ?              │ ← Branch scope
│   AND o.quotation_status = 'sales' │ ← Only confirmed
│   AND o.created_at BETWEEN ? AND ? │ ← Date range
│   AND (                            │
│     o.created_by = ? OR            │ ← Staff filter
│     o.staff_id = ?                 │
│   )                                │
│   AND o.user_id = ?                │ ← Customer filter
│ )                                  │
└────────────────────────────────────┘

Results Grouped by product_id
       ▼
Aggregated:
├─ SUM(oi.quantity) as qty_sold
├─ SUM(oi.total_amount) as amount
├─ COUNT(DISTINCT oi.order_id) as bills
└─ GET Product details

Sorted by qty_sold DESC
       ▼
Add rank numbers 1,2,3...
       ▼
Return as JSON
```

## Filter Logic Decision Tree

```
                    Apply Filters Click
                           ▼
                 ┌─────────────────────┐
                 │ Collect Parameters: │
                 │ • from_date         │
                 │ • to_date           │
                 │ • staff_id          │
                 │ • customer_id       │
                 └─────────────────────┘
                           ▼
                 ┌─────────────────────┐
        ┌────────►│ Start Building      │◄────────┐
        │         │ Query               │         │
        │         └─────────────────────┘         │
        │                 ▼                       │
        │         Date Range Filter               │
        │         (Always Applied)                │
        │                 ▼                       │
        │    Is staff_id provided?                │
        │    (Not "0" and not empty)              │
        │    /              \                     │
        │  YES              NO                    │
        │  /                  \                   │
   Staff   ──────────────────── No Staff         │
   WHERE   │                    Filter            │
   clause  │                                      │
           ▼                    ▼                 │
    Customer Check      Customer Check           │
    Is customer_id      Is customer_id           │
    provided?           provided?                │
    /      \            /      \                 │
  YES      NO         YES      NO                │
  /        \          /        \                 │
Both    Staff+   Customer   Date                 │
Filters  Date    Only       Only                 │
  │        │        │        │                   │
  └────────┴────────┴────────┘                   │
           ▼                                     │
    Execute Query                                │
           ▼                                     │
    Aggregate Results                            │
    (Group, Sum, Count, Rank)                    │
           ▼                                     │
    Return JSON to Frontend                      │
           ▼                                     │
    Render Tables & Cards◄───────────────────────┘
```

## API Response Flow

### Success Response Example

```json
{
  "status": true,
  "data": [
    {
      "rank": 1,
      "product_id": 123,
      "product_name": "SAMSUNG 25W POWER ADAPTER",
      "category": "charger",
      "sku": "SKU12345",
      "qty_sold": 5,
      "amount": 6099.00,
      "bills": 5
    },
    {
      "rank": 2,
      "product_id": 456,
      "product_name": "XIAOMI POWER BANK",
      "category": "power bank",
      "sku": "SKU67890",
      "qty_sold": 4,
      "amount": 1402.00,
      "bills": 3
    }
  ],
  "currency_symbol": "₹",
  "currency_position": "left",
  "count": 2
}
```

### Frontend Processing

```
JavaScript receives JSON
       ▼
Check response.status
   /           \
 true         false
  /             \
 ▼               ▼
Process         Show Error
Data            Message
 ▼               ▼
Iterate       Return
through        
response.data
 ▼
For each item:
├─ Create table row
├─ Format currency
├─ Escape HTML
└─ Append to table
 ▼
Show table
Hide loading
 ▼
Update complete ✓
```

## Concurrency Diagram

```
Timeline of Filter Application:

t=0.0s   User clicks "Apply Filters"
         │
         ├─────────────────────┬─────────────────────┬─────────────────────┐
         │                     │                     │                     │
t=0.1s   ▼ Summary Stats       ▼ Hot Items           ▼ Hot Customers      │
         AJAX Request sent     AJAX Request sent     AJAX Request sent    │
         │                     │                     │                     │
         │                     │                     │                     │
t=0.5s   │ Server Processing   │ Server Processing   │ Server Processing   │
         │ (Database Query)    │ (Database Query)    │ (Database Query)    │
         │                     │                     │                     │
         │                     │                     │                     │
t=0.8s   ▼ Response received   ▼ Response received   ▼ Response received  │
         Update Summary Cards  Update Hot Items tab  Update Hot Cust tab   │
         │                     │                     │                     │
         └─────────────────────┴─────────────────────┴─────────────────────┘
         ▼
t=1.0s   All UI Updated ✓
         Loading spinners hidden
         Page ready for user
```

## Multi-Tenant Support Diagram

```
                User Login
                    ▼
          ┌──────────────────────┐
          │ Determine Branch ID  │
          └──────────────────────┘
                    ▼
    ┌───────────────┬───────────────┬───────────────┐
    │               │               │               │
Admin Role      Sub-Admin Role    Staff Role
    │               │               │
    ├─ Use          ├─ Use own      ├─ Use own
    │ selected      │ branch_id     │ branch_id
    │ branch from   │               │
    │ session       │               │
    │               │               │
    └───────────────┴───────────────┴───────────────┘
                    ▼
        Apply Branch Filter to Query:
        WHERE orders.branch_id = ?
        AND users.branch_id = ?
                    ▼
        Only show data from user's branch
        Customers from other branches hidden
        Staff from other branches hidden
                    ▼
        Complete isolation between branches
        Even if user guesses another branch ID
        in URL parameters
```

## Performance Optimization

```
Frontend:
├─ Parallel AJAX calls (3 at once) ✓
├─ Client-side formatting (currency) ✓
└─ No full page reload ✓

Backend:
├─ Eager loading of relationships ✓
├─ Indexed queries on:
│  ├─ users.branch_id
│  ├─ orders.branch_id
│  ├─ orders.user_id
│  ├─ orders.staff_id
│  └─ orders.created_at
├─ Aggregation in database (not PHP) ✓
└─ Single query per endpoint ✓

Database:
├─ No N+1 queries ✓
├─ Efficient GROUP BY ✓
├─ WHERE before GROUP BY ✓
└─ Indexes on filtered columns ✓

Result: < 2 second response time
```

## Testing Matrix

```
                    Date Filter
                   /      |      \
              Applied  Modified  None
              
Staff Filter
    │         S-D         S-M      S-N
    │         │           │        │
Applied      All     Filter on   Only
             Combo   Staff+Date   Staff
    │
Modified     M-D        M-M       M-N
    │        Similar   Multiple  Filters
             Combos    Combos    Reset
    │
None         N-D        N-M       N-N
             Only      Only       All Data
             Date      Staff

Total: 9 combinations to test
(Add customer filter = 3×9 = 27 combinations)
```

This comprehensive architecture ensures:
✅ Scalability - Works with large datasets
✅ Maintainability - Clear separation of concerns
✅ Reliability - No single point of failure
✅ Performance - Optimized queries
✅ Security - Branch isolation, XSS prevention
✅ Extensibility - Easy to add more filters
