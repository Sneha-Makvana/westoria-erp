<?php

namespace App\Services;

use App\Models\DepartmentModel;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    public static function notify(
        ?int $userId,
        int $branchId,
        string $type,
        string $title,
        string $message,
        string $link = '#'
    ): ?Notification {
        if (empty($userId)) {
            return null;
        }

        return Notification::create([
            'user_id'   => $userId,
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'link'      => $link,
            'is_read'   => 0,
            'is_sound'  => 0,
            'branch_id' => $branchId,
        ]);
    }

    public static function notifyActorAndAssignee(
        ?int $actorId,
        ?int $assigneeId,
        int $branchId,
        string $type,
        string $actorTitle,
        string $actorMessage,
        string $assigneeTitle,
        string $assigneeMessage,
        string $link
    ): void {
        if ($actorId) {
            self::notify($actorId, $branchId, $type, $actorTitle, $actorMessage, $link);
        }

        if ($assigneeId && (int) $assigneeId !== (int) $actorId) {
            self::notify($assigneeId, $branchId, $type . '_assigned', $assigneeTitle, $assigneeMessage, $link);
        }
    }

    public static function notifyAssigneeIfChanged(
        ?int $actorId,
        ?int $previousAssigneeId,
        ?int $newAssigneeId,
        int $branchId,
        string $type,
        string $title,
        string $message,
        string $link
    ): void {
        if (empty($newAssigneeId) || (int) $newAssigneeId === (int) $previousAssigneeId) {
            return;
        }

        if ((int) $newAssigneeId === (int) $actorId) {
            return;
        }

        self::notify($newAssigneeId, $branchId, $type . '_assigned', $title, $message, $link);
    }

    public static function actorName(?int $userId): string
    {
        if (empty($userId)) {
            return 'Someone';
        }

        return User::where('id', $userId)->value('name') ?? 'Someone';
    }

    public static function notifyOrderAssigned(
        ?int $assigneeId,
        int $branchId,
        Order $order,
        ?User $customer = null
    ): void {
        if (empty($assigneeId)) {
            return;
        }

        $customerName = $customer?->name ?? 'N/A';

        self::notify(
            (int) $assigneeId,
            $branchId,
            'order_assigned',
            'Order Assigned to You',
            "Order #{$order->order_number} has been assigned to you for customer: {$customerName}.",
            '/sales-details/' . $order->id
        );
    }

    /** Notify all active warehouse staff in a branch (e.g. delivery orders). */
    public static function notifyWarehouseStaff(
        int $branchId,
        string $type,
        string $title,
        string $message,
        string $link,
        array $excludeUserIds = []
    ): void {
        $warehouseDepartmentIds = DepartmentModel::query()
            ->whereRaw('LOWER(department_name) LIKE ?', ['%warehouse%'])
            ->pluck('id');

        if ($warehouseDepartmentIds->isEmpty()) {
            return;
        }

        $excludeUserIds = array_values(array_filter(array_map('intval', $excludeUserIds)));

        $staffUsers = User::query()
            ->where('role', 'staff')
            ->where('isDeleted', 0)
            ->where(function ($query) {
                $query->where('status', 1)->orWhereNull('status');
            })
            ->where('branch_id', $branchId)
            ->whereHas('userDetail', function ($query) use ($warehouseDepartmentIds) {
                $query->whereIn('department_id', $warehouseDepartmentIds);
            })
            ->get(['id']);

        foreach ($staffUsers as $staffUser) {
            if (in_array((int) $staffUser->id, $excludeUserIds, true)) {
                continue;
            }

            self::notify((int) $staffUser->id, $branchId, $type, $title, $message, $link);
        }
    }

    public static function notifyDeliveryOrderWarehouse(
        int $branchId,
        Order $order,
        ?User $customer = null,
        array $excludeUserIds = []
    ): void {
        if (($order->order_type ?? '') !== 'delivery') {
            return;
        }

        if (($order->quotation_status ?? '') === 'quotation') {
            return;
        }

        $customerName = $customer?->name ?? 'N/A';

        self::notifyWarehouseStaff(
            $branchId,
            'delivery_order',
            'New Delivery Order',
            "Delivery order #{$order->order_number} for customer {$customerName} needs warehouse preparation.",
            '/sales-details/' . $order->id,
            $excludeUserIds
        );
    }
}
