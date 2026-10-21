<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentStore;
use Carbon\Carbon;

class PendingEmiService
{
    public static function parseMonth(?string $month): array
    {
        $now = Carbon::now('Asia/Kolkata');

        if ($month && preg_match('/^(\d{4})-(\d{2})$/', trim($month), $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [$now->year, $now->month];
    }

    public static function emiMonthLabel(int $month): string
    {
        return match ($month) {
            1 => '1st Month',
            2 => '2nd Month',
            3 => '3rd Month',
            default => "{$month}th Month",
        };
    }

    public static function getPendingEmis(int $branchId, int $year, int $month): array
    {
        $orders = Order::with(['user:id,name,phone'])
            ->where('branch_id', $branchId)
            ->where(function ($query) {
                $query->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->where(function ($query) {
                $query->whereRaw('LOWER(COALESCE(payment_method, "")) = ?', ['emi'])
                    ->orWhere(function ($inner) {
                        $inner->whereNotNull('emi_tenure')
                            ->where('emi_tenure', '!=', '')
                            ->where('emi_tenure', '!=', '0');
                    })
                    ->orWhere('emi_monthly_amount', '>', 0);
            })
            ->orderByDesc('created_at')
            ->get();

        if ($orders->isEmpty()) {
            return [];
        }

        $paidByOrder = PaymentStore::query()
            ->whereIn('order_id', $orders->pluck('id'))
            ->where(function ($query) {
                $query->whereNull('isDeleted')->orWhere('isDeleted', 0);
            })
            ->where(function ($query) {
                $query->where('payment_method', 'emi')->orWhere('payment_type', 'emi');
            })
            ->whereNotNull('emi_month')
            ->get(['order_id', 'emi_month'])
            ->groupBy('order_id')
            ->map(function ($rows) {
                return $rows->pluck('emi_month')
                    ->map(fn ($value) => (int) $value)
                    ->filter(fn ($value) => $value > 0)
                    ->unique()
                    ->values();
            });

        $results = [];

        foreach ($orders as $order) {
            $tenure = (int) preg_replace('/[^0-9]/', '', (string) ($order->emi_tenure ?? ''));
            $monthlyAmount = (float) ($order->emi_monthly_amount ?? 0);

            if ($tenure <= 0 || $monthlyAmount <= 0) {
                continue;
            }

            $paidMonths = $paidByOrder->get($order->id, collect());
            $startDate = Carbon::parse($order->created_at)->timezone('Asia/Kolkata')->startOfMonth();

            $nextDueMonth = 1;
            while ($nextDueMonth <= $tenure && $paidMonths->contains($nextDueMonth)) {
                $nextDueMonth++;
            }

            for ($emiMonth = 1; $emiMonth <= $tenure; $emiMonth++) {
                $dueDate = $startDate->copy()->addMonths($emiMonth - 1);

                if ($dueDate->year !== $year || $dueDate->month !== $month || $paidMonths->contains($emiMonth)) {
                    continue;
                }

                $results[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->user?->name ?? 'N/A',
                    'customer_phone' => $order->user?->phone ?? 'N/A',
                    'emi_amount' => $monthlyAmount,
                    'emi_month' => $emiMonth,
                    'emi_month_label' => self::emiMonthLabel($emiMonth),
                    'due_date' => $dueDate->format('d-m-Y'),
                    'can_pay' => $emiMonth === $nextDueMonth,
                    'next_due_month' => $nextDueMonth <= $tenure ? $nextDueMonth : null,
                    'remaining_amount' => (float) ($order->remaining_amount ?? 0),
                ];

                break;
            }
        }

        usort($results, function ($left, $right) {
            return strcmp((string) ($left['order_number'] ?? ''), (string) ($right['order_number'] ?? ''));
        });

        return $results;
    }
}
