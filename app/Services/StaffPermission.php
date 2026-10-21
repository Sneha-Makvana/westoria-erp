<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StaffPermission
{
    public static function hasFullAccess(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->role === 'admin'
            || $user->role === 'sub-admin'
            || (int) $user->role_id === 1;
    }

    public static function actionColumn(string $action): ?string
    {
        return match ($action) {
            'read', 'view' => 'view',
            'create', 'add' => 'add',
            'edit', 'update' => 'edit',
            'delete' => 'delete',
            default => null,
        };
    }

    public static function check(int $moduleId, string $action, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return false;
        }

        if (self::hasFullAccess($user)) {
            return true;
        }

        $column = self::actionColumn($action);

        if ($column === null) {
            return false;
        }

        return UserPermission::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where($column, 1)
            ->exists();
    }

    /** @return array<string, bool> */
    public static function moduleFlags(int $moduleId, ?User $user = null): array
    {
        return [
            'view' => self::check($moduleId, 'view', $user),
            'add' => self::check($moduleId, 'add', $user),
            'edit' => self::check($moduleId, 'edit', $user),
            'delete' => self::check($moduleId, 'delete', $user),
        ];
    }

    /** @return array<int, array<string, bool>> */
    public static function allModuleFlags(?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (! $user || self::hasFullAccess($user)) {
            return [];
        }

        $map = [];

        foreach (UserPermission::where('user_id', $user->id)->get() as $permission) {
            $map[(int) $permission->module_id] = [
                'view' => (bool) $permission->view,
                'add' => (bool) $permission->add,
                'edit' => (bool) $permission->edit,
                'delete' => (bool) $permission->delete,
            ];
        }

        return $map;
    }

    public static function denyApiUnless(int $moduleId, string $action, ?User $user = null): ?JsonResponse
    {
        if (! self::check($moduleId, $action, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission for this action.',
            ], 403);
        }

        return null;
    }
}
