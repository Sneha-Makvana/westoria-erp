<?php

namespace App\Http\Middleware;

use App\Services\StaffDepartmentScope;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AutoPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Keep full access for admin/sub-admin roles.
        if (
            $user->role === 'admin' ||
            $user->role === 'sub-admin' ||
            (int) $user->role_id === 1
        ) {
            return $next($request);
        }

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $normalizedRouteName = $routeName ? strtolower($routeName) : null;

        if (!$normalizedRouteName) {
            return $next($request);
        }

        $getPermission = static function (int $moduleId) use ($user) {
            return DB::table('user_permissions')
                ->where('user_id', $user->id)
                ->where('module_id', $moduleId)
                ->first();
        };

        // Profit & Loss report uses dedicated report module permission.
        if (in_array($normalizedRouteName, [
            'profit-loss-report.index',
            'profit-loss-report.data',
            'profit-loss-report.pdf',
        ], true)) {
            $permission = $getPermission(37);

            if (!$permission || (int) ($permission->view ?? 0) !== 1) {
                return redirect()->route('auth.profile')
                    ->with('error', 'You do not have permission to access this page');
            }

            return $next($request);
        }

        // Dedicated report modules (view = page access).
        $reportRouteModuleMap = [
            'sales.report' => 34,
            'sale.report.exportpdf' => 34,
            'tds.report' => 38,
            'purchase.report' => 35,
            'purchase.report.exportpdf' => 35,
            'expense.report' => 36,
            'expense.report.exportpdf' => 36,
        ];

        if (isset($reportRouteModuleMap[$normalizedRouteName])) {
            $permission = $getPermission($reportRouteModuleMap[$normalizedRouteName]);

            if (!$permission || (int) ($permission->view ?? 0) !== 1) {
                return redirect()->route('auth.profile')
                    ->with('error', 'You do not have permission to access this page');
            }

            return $next($request);
        }

        // Receipt & Payment listing/ajax pages should be accessible with view OR add on Receipt and Payment (46).
        if (in_array($normalizedRouteName, [
            'sales.receipt.index',
            'sales.receipt.orders',
            'sales.receipt.vendor-invoices',
        ], true)) {
            $receiptPermission = $getPermission(46);
            $canView = (int) ($receiptPermission->view ?? 0) === 1;
            $canAdd = (int) ($receiptPermission->add ?? 0) === 1;

            if (!$canView && !$canAdd) {
                return redirect()->route('auth.profile')
                    ->with('error', 'You do not have permission to access this page');
            }

            return $next($request);
        }

        $parts = explode('.', $normalizedRouteName);

        if (count($parts) < 2) {
            return $next($request);
        }

        $module = $parts[0];
        $action = end($parts);

        // Always allow staff dashboard/profile.
        if ($module === 'auth' && in_array($action, ['dashboard', 'profile', 'staff-dashboard'], true)) {
            return $next($request);
        }

        // Notifications list/detail should be available to authenticated staff users.
        if ($module === 'notifications') {
            return $next($request);
        }

        // Global header search should be available for authenticated users.
        if ($normalizedRouteName === 'users.ajaxsearch') {
            return $next($request);
        }

        if ($user->role === 'staff' && in_array($normalizedRouteName, [
            // Removed hardcoded products delivery status bypass, now handled via module 39
        ], true) && StaffDepartmentScope::showProductsDelivery($user)) {
            return $next($request);
        }

        // Staff can always access their own workspace features (attendance, leaves, payroll).
        if ($user->role === 'staff' && in_array($normalizedRouteName, [
            'attendance.list',
            'attendence.calendar',
            'leave.request',
            'leave.add',
            'leave.request.status',
            'payroll.list',
            'salary.list',
            'salary.view',
            'staff.checkstatus',
            'staff.checkin',
            'staff.checkout'
        ], true)) {
            return $next($request);
        }

        // Explicit route-name mapping for cases where module prefix is not enough.
        $routeNameModuleMap = [
            'auth.taxrates' => 15,
            'auth.currency' => 15,
            'sales.receipt.index' => 46,
            'sales.receipt.orders' => 46,
            'sales.receipt.vendor-invoices' => 46,
            'sales.receipt.store' => 46,
            'accounting.balance-sheet' => 44,
            'sales.products_delivery' => 39,
            'sales.products_delivery.data' => 39,
            'sales.delivery' => 39,
            'sales.delivery.status.update' => 39,
            'sales.order.delivery.status.update' => 39,
            'sales.delivery.challan.pdf' => 39,
            
            // Manufacturing ERP explicit routes mapping (avoid clashing with inventory module)
            'inventory.bom.list' => 49,
            'inventory.bom.add' => 49,
            'inventory.bom.view' => 49,
            'inventory.bom.edit' => 49,
            'inventory.production.list' => 49,
            'inventory.production.add' => 49,
            'inventory.production.edit' => 49,
            'inventory.production.view' => 49,
        ];

        $moduleIdMap = [
            'product' => 1,
            'category' => 6,
            'brand' => 6,
            'unit' => 6,
            'labour_item' => 6,
            'sale' => 2,
            'sales' => 2,
            'salesreturn' => 2,
            'quotation' => 2,
            'purchase' => 3,
            'purchasereturn' => 3,
            'invoice' => 4,
            'custom_invoice' => 4,
            'custom-invoice' => 4,
            'expense' => 5,
            'expensetype' => 5,
            'ticket' => 33,
            'staff' => 8,
            'salary' => 8,
            'subbranch' => 8,
            'customer' => 9,
            'vendor' => 10,
            'financer' => 40,
            'lead' => 32,
            'setting' => 14,
            'account_ledger' => 42,
            'accounting' => 42,
            'income-statement' => 43,
            'banks' => 45,
            'inventory' => 41,
            'row_material' => 49,
            'material-inventory' => 49,
            'cash-bank-book' => 47,
            'appointments' => 17,
            'followup' => 30,
            'meeting' => 31,
            'gst' => 20,
            'exports' => 20,
            'advance_pay' => 23,
            'attendance' => 26,
            'attendence' => 26,
            // 'leave' => 28,
            // 'payroll' => 29,
            'transaction' => 47,
            'credit-notes' => 48,
            'credit-notes-items' => 48,
            'debit-notes-items' => 48,
            'leave' => 28,
            'payroll' => 29,
        ];

        $moduleId = $routeNameModuleMap[$normalizedRouteName] ?? ($moduleIdMap[$module] ?? null);

        if (!$moduleId) {
            return redirect()->route('auth.profile')
                ->with('error', 'You do not have permission to access this page');
        }

        // Returns are restricted more tightly: require both add + edit on parent module.
        if (in_array($module, ['salesreturn', 'purchasereturn'], true)) {
            $returnsPermission = $getPermission($moduleId);

            if (
                !$returnsPermission ||
                (int) ($returnsPermission->add ?? 0) !== 1 ||
                (int) ($returnsPermission->edit ?? 0) !== 1
            ) {
                return redirect()->route('auth.profile')
                    ->with('error', 'You do not have permission to access this page');
            }

            return $next($request);
        }

        $actionMap = [
            'list' => 'view',
            'index' => 'view',
            'show' => 'view',
            'view' => 'view',

            'add' => 'add',
            'create' => 'add',
            'store' => 'add',

            'edit' => 'edit',
            'update' => 'edit',

            'delete' => 'delete',
            'destroy' => 'delete',
        ];

        // Non-CRUD actions (report/pdf/export/etc.) should still require view permission.
        $action = $actionMap[$action] ?? 'view';

        $permission = $getPermission($moduleId);

        if (!$permission || (int) ($permission->$action ?? 0) !== 1) {
            return redirect()->route('auth.profile')
                ->with('error', 'You do not have permission to access this page');
        }

        return $next($request);
    }
}
