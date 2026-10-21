<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationQuery;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * GET /notifications (AJAX endpoint for dropdown)
     * Get latest notifications with branch-wise filtering
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        }

        $formatNotification = function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'link' => $notification->link ?? '#',
                'is_read' => (bool) $notification->is_read,
                'created_at' => $notification->created_at,
                'type' => $notification->type,
                'branch_id' => $notification->branch_id,
                'user_id' => $notification->user_id,
            ];
        };

        $grouped = [];
        foreach (['login', 'products', 'other'] as $group) {
            $groupQuery = NotificationQuery::forUser($user)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc');
            NotificationQuery::applyGroup($groupQuery, $group);
            $grouped[$group] = $groupQuery->take(4)->get()->map($formatNotification)->values();
        }

        $formattedNotifications = $grouped['login']
            ->concat($grouped['products'])
            ->concat($grouped['other'])
            ->values();

        $unreadCount = NotificationQuery::forUser($user)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'data' => $formattedNotifications,
            'groups' => $grouped,
            'count' => $unreadCount,
            'total_unread' => $unreadCount
        ]);
    }

    /**
     * GET /notifications/all (or /notifications/index)
     * Get all notifications for the list page with pagination and branch-wise filtering
     */
    public function getAllNotifications(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $query = NotificationQuery::forUser($user)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('group')) {
            NotificationQuery::applyGroup($query, (string) $request->group);
        } elseif ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        // Handle AJAX request for paginated data
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $perPage = $request->get('per_page', 10);
            $notifications = $query->paginate($perPage);

            $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => (int) $notification->id,
                    'title' => (string) $notification->title,
                    'message' => (string) $notification->message,
                    'link' => (string) ($notification->link ?? '#'),
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => (string) $notification->created_at,
                    'type' => (string) $notification->type,
                    'branch_id' => $notification->branch_id,
                    'user_id' => $notification->user_id,
                    'formatted_date' => $notification->created_at ? Carbon::parse($notification->created_at)->format('d M Y') : '-',
                    'formatted_time' => $notification->created_at ? Carbon::parse($notification->created_at)->format('h:i A') : '-'
                ];
            })->values()->all();

            $notificationTypes = NotificationQuery::forUser($user)
                ->distinct()
                ->pluck('type')
                ->filter()
                ->values()
                ->all();

            return response()->json([
                'status' => true,
                'data' => $formattedNotifications,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
                'notification_types' => $notificationTypes
            ]);
        }

        // For initial view (non-AJAX)
        $perPage = 10;
        $notifications = $query->paginate($perPage);

        $notificationTypes = NotificationQuery::forUser($user)
            ->distinct()
            ->pluck('type');

        return view('notifications.index', compact('notifications', 'notificationTypes'));
    }

    /**
     * GET /notifications/live-updates
     * Poll for new notifications (user-wise) for browser alerts + badge count.
     */
    public function liveUpdates(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $afterId = max(0, (int) $request->query('after_id', 0));
        $baseQuery = NotificationQuery::forUser($user);

        $newNotifications = (clone $baseQuery)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit(20)
            ->get()
            ->map(fn ($notification) => NotificationQuery::format($notification))
            ->values()
            ->all();

        $unreadCount = (clone $baseQuery)->where('is_read', false)->count();
        $maxId = (clone $baseQuery)->max('id') ?? 0;

        return response()->json([
            'status' => true,
            'new_notifications' => $newNotifications,
            'unread_count' => $unreadCount,
            'max_id' => (int) $maxId,
        ]);
    }

    /**
     * POST /notifications/{id}/read
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;

            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if ((int) $notification->user_id !== (int) $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to mark this notification as read'
                ], 403);
            }

            $notification->update(['is_read' => 1]);

            // Get updated unread count
            $unreadCount = $this->getUnreadCount($user);

            return response()->json([
                'status' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error marking notification as read'
            ], 500);
        }
    }

    /**
     * POST /notifications/mark-all-read
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = auth()->user();

            $updatedCount = NotificationQuery::forUser($user)
                ->where('is_read', false)
                ->update(['is_read' => 1]);

            return response()->json([
                'status' => true,
                'message' => $updatedCount . ' notifications marked as read',
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Mark all as read error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error marking notifications as read'
            ], 500);
        }
    }

    /**
     * DELETE /notifications/{id}/delete
     * Delete a single notification
     */
    public function delete($id)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;

            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if ((int) $notification->user_id !== (int) $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to delete this notification'
                ], 403);
            }

            $notification->delete();

            // Get updated unread count
            $unreadCount = $this->getUnreadCount($user);

            return response()->json([
                'status' => true,
                'message' => 'Notification deleted successfully',
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error('Delete notification error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error deleting notification'
            ], 500);
        }
    }

    /**
     * DELETE /notifications/delete-all
     * Delete all notifications
     */
    public function deleteAll(Request $request)
    {
        try {
            $user = auth()->user();

            $deletedCount = NotificationQuery::forUser($user)->delete();

            return response()->json([
                'status' => true,
                'message' => $deletedCount . ' notifications deleted successfully',
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Delete all notifications error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error deleting notifications'
            ], 500);
        }
    }

    /**
     * Helper function to get unread count based on user role and branch
     */
    private function getUnreadCount($user, $selectedSubAdminId = null)
    {
        return NotificationQuery::forUser($user)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Helper function to format date
     */
    private function formatDate($date)
    {
        if ($date->isToday()) {
            return 'Today';
        } elseif ($date->isYesterday()) {
            return 'Yesterday';
        } else {
            return $date->format('d M Y');
        }
    }
}
