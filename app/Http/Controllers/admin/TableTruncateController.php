<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableTruncateController extends Controller
{
    private array $fullTruncateTables = ['modules', 'payment_store'];
    private array $customBranchTables = ['sales_labour_items', 'sales_return_items', 'user_details', 'user_permissions'];
    private array $excludedTables = ['settings'];
    private array $bulkPriorityTables = ['user_permissions', 'user_details', 'sales_labour_items', 'sales_return_items', 'users'];

    private function resolveBranchId(): int
    {
        $user = Auth::user();
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($user->role === 'staff' && $user->branch_id) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && !empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) $user->id;
    }

    public function index()
    {
        $allTables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $dbName;
        $branchId = $this->resolveBranchId();

        $tables = collect($allTables)->map(function ($row) use ($tableKey, $branchId) {
            $tableName = $row->$tableKey ?? null;
            if (!$tableName) {
                return null;
            }
            if (in_array($tableName, $this->excludedTables, true)) {
                return null;
            }

            $hasBranchColumn = Schema::hasColumn($tableName, 'branch_id');
            $hasCreatedByColumn = Schema::hasColumn($tableName, 'created_by');
            $allowFullTruncate = in_array($tableName, $this->fullTruncateTables, true);
            $count = null;
            $isCustomBranchTable = in_array($tableName, $this->customBranchTables, true);

            if ($hasBranchColumn) {
                if ($tableName === 'users' && Schema::hasColumn('users', 'role')) {
                    $count = DB::table('users')
                        ->where('branch_id', $branchId)
                        ->whereRaw('LOWER(role) <> ?', ['admin'])
                        ->count();
                } else {
                    $count = DB::table($tableName)->where('branch_id', $branchId)->count();
                }
            } elseif ($hasCreatedByColumn) {
                $count = DB::table($tableName)->where('created_by', $branchId)->count();
            } elseif ($isCustomBranchTable) {
                if ($tableName === 'sales_labour_items') {
                    $count = DB::table('sales_labour_items')
                        ->whereExists(function ($query) use ($branchId) {
                            $query->select(DB::raw(1))
                                ->from('orders as o')
                                ->whereColumn('o.id', 'sales_labour_items.order_id')
                                ->where('o.branch_id', $branchId);
                        })
                        ->whereExists(function ($query) use ($branchId) {
                            $query->select(DB::raw(1))
                                ->from('labour_items as li')
                                ->whereColumn('li.id', 'sales_labour_items.labour_item_id')
                                ->where('li.created_by', $branchId);
                        })
                        ->count();
                } elseif ($tableName === 'sales_return_items') {
                    $count = DB::table('sales_return_items')
                        ->whereExists(function ($query) use ($branchId) {
                            $query->select(DB::raw(1))
                                ->from('sales_returns as sr')
                                ->whereColumn('sr.id', 'sales_return_items.sales_return_id')
                                ->where('sr.branch_id', $branchId);
                        })
                        ->count();
                } elseif ($tableName === 'user_details') {
                    $count = DB::table('user_details')
                        ->whereExists(function ($query) use ($branchId) {
                            $query->select(DB::raw(1))
                                ->from('users as u')
                                ->whereColumn('u.id', 'user_details.user_id')
                                ->where(function ($sub) use ($branchId) {
                                    $sub->where('u.branch_id', $branchId)
                                        ->orWhere('u.id', $branchId);
                                });
                        })
                        ->count();
                } elseif ($tableName === 'user_permissions') {
                    $count = DB::table('user_permissions')
                        ->whereExists(function ($query) use ($branchId) {
                            $query->select(DB::raw(1))
                                ->from('users as u')
                                ->whereColumn('u.id', 'user_permissions.user_id')
                                ->where(function ($sub) use ($branchId) {
                                    $sub->where('u.branch_id', $branchId)
                                        ->orWhere('u.id', $branchId);
                                });
                        })
                        ->whereExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('modules as m')
                                ->whereColumn('m.id', 'user_permissions.module_id');
                        })
                        ->count();
                }
            } elseif ($allowFullTruncate) {
                $count = DB::table($tableName)->count();
            }

            return [
                'name' => $tableName,
                'has_branch_id' => ($hasBranchColumn || $hasCreatedByColumn || $allowFullTruncate || $isCustomBranchTable),
                'branch_records' => $count,
            ];
        })->filter()->values();

        return view('settings.table-truncate', compact('tables', 'branchId'));
    }

    public function truncate(string $table)
    {
        $branchId = $this->resolveBranchId();

        if (!Schema::hasTable($table)) {
            return redirect()->route('table-truncate.index')->with('error', 'Table not found.');
        }
        if (in_array($table, $this->excludedTables, true)) {
            return redirect()->route('table-truncate.index')->with('error', "Table '{$table}' is protected and cannot be truncated.");
        }

        $hasBranchColumn = Schema::hasColumn($table, 'branch_id');
        $hasCreatedByColumn = Schema::hasColumn($table, 'created_by');
        $allowFullTruncate = in_array($table, $this->fullTruncateTables, true);
        $isCustomBranchTable = in_array($table, $this->customBranchTables, true);

        if (!$hasBranchColumn && !$hasCreatedByColumn && !$allowFullTruncate && !$isCustomBranchTable) {
            return redirect()->route('table-truncate.index')->with('error', "Table '{$table}' does not support branch-wise truncate.");
        }

        $result = $this->truncateTableByRules($table, $branchId);
        if (!$result['status']) {
            return redirect()->route('table-truncate.index')->with('error', $result['message']);
        }

        $message = $allowFullTruncate && !$hasBranchColumn && !$hasCreatedByColumn
            ? "All data truncated for table '{$table}'."
            : "Branch-wise data truncated for table '{$table}'.";

        return redirect()->route('table-truncate.index')->with('success', $message);
    }

    public function truncateAll()
    {
        $branchId = $this->resolveBranchId();
        $allTables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $dbName;

        $tables = collect($allTables)
            ->map(fn($row) => $row->$tableKey ?? null)
            ->filter()
            ->reject(fn($name) => in_array($name, $this->excludedTables, true))
            ->values()
            ->all();

        $ordered = collect($this->bulkPriorityTables)
            ->filter(fn($name) => in_array($name, $tables, true))
            ->values()
            ->all();

        foreach ($tables as $table) {
            if (!in_array($table, $ordered, true)) {
                $ordered[] = $table;
            }
        }

        $done = 0;
        $failed = [];

        foreach ($ordered as $table) {
            $hasBranchColumn = Schema::hasColumn($table, 'branch_id');
            $hasCreatedByColumn = Schema::hasColumn($table, 'created_by');
            $allowFullTruncate = in_array($table, $this->fullTruncateTables, true);
            $isCustomBranchTable = in_array($table, $this->customBranchTables, true);

            if (!$hasBranchColumn && !$hasCreatedByColumn && !$allowFullTruncate && !$isCustomBranchTable) {
                continue;
            }

            $result = $this->truncateTableByRules($table, $branchId);
            if ($result['status']) {
                $done++;
            } else {
                $failed[] = $table;
            }
        }

        if (!empty($failed)) {
            return redirect()->route('table-truncate.index')->with(
                'error',
                "Bulk truncate completed with some skipped tables. Truncated: {$done}. Skipped: " . implode(', ', $failed) . '.'
            );
        }

        return redirect()->route('table-truncate.index')->with('success', "Bulk truncate completed successfully. Total tables truncated: {$done}.");
    }

    private function truncateTableByRules(string $table, int $branchId): array
    {
        $hasBranchColumn = Schema::hasColumn($table, 'branch_id');
        $hasCreatedByColumn = Schema::hasColumn($table, 'created_by');
        $isCustomBranchTable = in_array($table, $this->customBranchTables, true);

        if ($hasBranchColumn) {
            if ($table === 'users' && Schema::hasColumn('users', 'role')) {
                DB::table('users')
                    ->where('branch_id', $branchId)
                    ->whereRaw('LOWER(role) <> ?', ['admin'])
                    ->delete();
            } else {
                DB::table($table)->where('branch_id', $branchId)->delete();
            }
            return ['status' => true];
        }

        if ($hasCreatedByColumn) {
            DB::table($table)->where('created_by', $branchId)->delete();
            return ['status' => true];
        }

        if ($isCustomBranchTable && $table === 'sales_labour_items') {
            $linkedRecordsCount = DB::table('sales_labour_items')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('orders as o')
                        ->whereColumn('o.id', 'sales_labour_items.order_id');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('labour_items as li')
                        ->whereColumn('li.id', 'sales_labour_items.labour_item_id');
                })
                ->count();

            if ($linkedRecordsCount > 0) {
                return ['status' => false, 'message' => "Cannot truncate 'sales_labour_items'. Related records still exist in orders and labour_items."];
            }

            DB::table('sales_labour_items')
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('orders as o')
                        ->whereColumn('o.id', 'sales_labour_items.order_id')
                        ->where('o.branch_id', $branchId);
                })
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('labour_items as li')
                        ->whereColumn('li.id', 'sales_labour_items.labour_item_id')
                        ->where('li.created_by', $branchId);
                })
                ->delete();
            return ['status' => true];
        }

        if ($isCustomBranchTable && $table === 'sales_return_items') {
            $linkedRecordsCount = DB::table('sales_return_items')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('sales_returns as sr')
                        ->whereColumn('sr.id', 'sales_return_items.sales_return_id');
                })
                ->count();

            if ($linkedRecordsCount > 0) {
                return ['status' => false, 'message' => "Cannot truncate 'sales_return_items'. Related records still exist in sales_returns."];
            }

            DB::table('sales_return_items')
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('sales_returns as sr')
                        ->whereColumn('sr.id', 'sales_return_items.sales_return_id')
                        ->where('sr.branch_id', $branchId);
                })
                ->delete();
            return ['status' => true];
        }

        if ($isCustomBranchTable && $table === 'user_details') {
            $linkedRecordsCount = DB::table('user_details')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('users as u')
                        ->whereColumn('u.id', 'user_details.user_id');
                })
                ->count();

            if ($linkedRecordsCount > 0) {
                return ['status' => false, 'message' => "Cannot truncate 'user_details'. Related records still exist in users."];
            }

            DB::table('user_details')
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('users as u')
                        ->whereColumn('u.id', 'user_details.user_id')
                        ->where(function ($sub) use ($branchId) {
                            $sub->where('u.branch_id', $branchId)
                                ->orWhere('u.id', $branchId);
                        });
                })
                ->delete();
            return ['status' => true];
        }

        if ($isCustomBranchTable && $table === 'user_permissions') {
            $linkedRecordsCount = DB::table('user_permissions')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('users as u')
                        ->whereColumn('u.id', 'user_permissions.user_id');
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('modules as m')
                        ->whereColumn('m.id', 'user_permissions.module_id');
                })
                ->count();

            if ($linkedRecordsCount > 0) {
                return ['status' => false, 'message' => "Cannot truncate 'user_permissions'. Related records still exist in users and modules."];
            }

            DB::table('user_permissions')
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('users as u')
                        ->whereColumn('u.id', 'user_permissions.user_id')
                        ->where(function ($sub) use ($branchId) {
                            $sub->where('u.branch_id', $branchId)
                                ->orWhere('u.id', $branchId);
                        });
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('modules as m')
                        ->whereColumn('m.id', 'user_permissions.module_id');
                })
                ->delete();
            return ['status' => true];
        }

        DB::table($table)->delete();
        return ['status' => true];
    }

}
