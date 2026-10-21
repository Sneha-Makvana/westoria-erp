<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class NotificationQuery
{
    /** Notifications belong to the logged-in user only. */
    public static function forUser(User $user): Builder
    {
        return Notification::query()->where('user_id', $user->id);
    }

    public static function loginTypes(): array
    {
        return ['login', 'vendor_login'];
    }

    public static function productTypes(): array
    {
        return [
            'product',
            'product_created',
            'product_updated',
            'product_deleted',
            'product_restored',
            'price_changed',
            'price_update_request',
        ];
    }

    public static function applyGroup(Builder $query, string $group): Builder
    {
        $login = self::loginTypes();
        $products = self::productTypes();

        if ($group === 'login') {
            return $query->whereIn('type', $login);
        }

        if ($group === 'products') {
            return $query->where(function ($inner) use ($products) {
                $inner->whereIn('type', $products)
                    ->orWhere('type', 'like', 'product%');
            });
        }

        if ($group === 'other') {
            return $query->where(function ($inner) use ($login, $products) {
                $inner->whereNull('type')
                    ->orWhere(function ($rest) use ($login, $products) {
                        $rest->whereNotIn('type', array_merge($login, $products))
                            ->where('type', 'not like', 'product%');
                    });
            });
        }

        return $query;
    }

    public static function format(Notification $notification): array
    {
        return [
            'id' => (int) $notification->id,
            'title' => (string) $notification->title,
            'message' => (string) $notification->message,
            'link' => (string) ($notification->link ?? '#'),
            'is_read' => (bool) $notification->is_read,
            'type' => (string) $notification->type,
            'created_at' => $notification->created_at?->toIso8601String()
                ?? (string) $notification->created_at,
        ];
    }
}
