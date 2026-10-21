import sys

index_path = 'c:/xampp/htdocs/inventory-billing/resources/views/index.blade.php'
staff_path = 'c:/xampp/htdocs/inventory-billing/resources/views/staff_dashboard.blade.php'

with open(index_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

recent_sales = ''.join(lines[2361:2447])
recent_purchases = ''.join(lines[2448:2534])
products_delivery = ''.join(lines[2663:2749])

# Need to change some logic in the blocks to use the staff dashboard's $canViewXXX variables
recent_sales = recent_sales.replace(
    "$showErpDashboard &&", 
    ""
).replace(
    "in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&",
    "$canViewSales &&"
)

recent_purchases = recent_purchases.replace(
    "$showErpDashboard &&", 
    ""
).replace(
    "in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&",
    "$canViewPurchases &&"
)

products_delivery = products_delivery.replace(
    "@if ($showErpProductsDeliveryCard)",
    "@if ($canViewSales && $showErpProductsDelivery)"
).replace(
    "{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex",
    "col-md-4"
).replace("flex-fill", "mb-4")

insert_text = recent_sales + '\n' + recent_purchases + '\n' + products_delivery + '\n'

with open(staff_path, 'r', encoding='utf-8') as f:
    staff_content = f.read()

target = '<div class="row mt-4 mb-4">'
if target in staff_content:
    staff_content = staff_content.replace(target, target + '\n' + insert_text)
    with open(staff_path, 'w', encoding='utf-8') as f:
        f.write(staff_content)
    print('Successfully inserted blocks.')
else:
    print('Target not found in staff_dashboard.blade.php')
