<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductInventory;
use App\Models\RentalReturn;
use App\Models\RentalReturnItem;
use App\Models\PaymentStore;
use App\Models\Product;
use App\Models\User;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentalReturnController extends Controller
{
    private function getBranchContext(): array
    {
        $user = auth()->user() ?? auth('api')->user();

        $userId = $user->id;
        $role = $user->role;
        $userBranch = $user->branch_id;
        $selectedBranch = session('selectedSubAdminId');

        if ($role === 'admin' && $selectedBranch) {
            $branchId = $selectedBranch;
        } elseif ($role === 'sub-admin') {
            $branchId = $userId;
        } elseif ($role === 'staff') {
            $branchId = $userBranch;
        } else {
            $branchId = $userId;
        }

        return [$user, $userId, $role, $branchId];
    }

    public function rental_return_list(Request $request)
    {
        return view('rentalreturn.rentalreturnlist');
    }

    public function rentalReturnList(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userId       = (int) $authUser->id;
        $role         = $authUser->role;
        $userBranchId = $authUser->branch_id;

        $selectedSubAdminId = $request->query('selectedSubAdminId');
        if (in_array($selectedSubAdminId, ['null', 'undefined', ''], true)) {
            $selectedSubAdminId = null;
        }

        if ($role === 'staff' && $userBranchId) {
            $branchIdToUse = (int) $userBranchId;
        } elseif ($role === 'admin' && !empty($selectedSubAdminId)) {
            $branchIdToUse = (int) $selectedSubAdminId;
        } elseif ($role === 'sub-admin') {
            $branchIdToUse = $userId;
        } else {
            $branchIdToUse = $userId;
        }

        $query = RentalReturn::with([
            'order:id,order_number,user_id,total_amount,deposit_amount',
            'order.user:id,name,phone',
            'items',
        ])->orderBy('id', 'desc');

        if ($role === 'staff') {
            $query->where('returned_by', $userId);
        } else {
            $query->where('branch_id', $branchIdToUse);
        }

        $search = $request->query('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($oq) use ($search) {
                    $oq->where('order_number', 'LIKE', "%{$search}%")
                       ->orWhereHas('user', function ($uq) use ($search) {
                           $uq->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('phone', 'LIKE', "%{$search}%");
                       });
                });
            });
        }

        $perPage      = (int) $request->query('per_page', 10);
        $rentalReturns = $query->paginate($perPage);

        $settings         = DB::table('settings')->where('branch_id', $branchIdToUse)->first();
        $currencySymbol   = $settings->currency_symbol   ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $data = $rentalReturns->map(function ($rr) {
            $order    = $rr->order;
            $customer = $order?->user?->name ?? '-';

            $itemsData = $rr->items->map(function ($item) {
                $product = Product::find($item->product_id);
                return [
                    'product_id'   => $item->product_id,
                    'product_name' => $product->name ?? 'N/A',
                    'qty_returned' => (float) $item->qty_returned,
                    'condition'    => $item->condition ?? 'good',
                    'damage_charge'=> (float) $item->damage_charge,
                    'late_fee'     => (float) $item->late_fee,
                ];
            });

            $totalQty = $rr->items->sum('qty_returned');

            return [
                'id'                     => $rr->id,
                'order_id'               => $order?->id,
                'order_number'           => $order?->order_number ?? '-',
                'return_date'            => optional($rr->return_date)->format('d M Y') ?? ($rr->return_date ?? '-'),
                'customer'               => $customer,
                'items_count'            => (int) $rr->items->count(),
                'total_qty_returned'     => (float) $totalQty,
                'final_settlement_amount'=> (float) $rr->final_settlement_amount,
                'payment_method'         => $rr->payment_method ?? '-',
                'is_full_return'         => (bool) $rr->is_full_return,
                'deposit_settled'        => (bool) $rr->deposit_settled,
                'deposit_amount_applied' => (float) $rr->deposit_amount_applied,
                'remarks'                => $rr->remarks ?? '',
                'items'                  => $itemsData,
            ];
        })->values();

        return response()->json([
            'status'            => true, 
            'currency_symbol'   => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data'              => $data,
            'pagination'        => [
                'current_page' => $rentalReturns->currentPage(),
                'last_page'    => $rentalReturns->lastPage(),
                'per_page'     => $rentalReturns->perPage(),
                'total'        => $rentalReturns->total(),
            ],
        ]);
    }

    public function create_rental_return(Request $request)
    {        [, $userId, $role, $branchId] = $this->getBranchContext();

        // Get rental orders
        $invoiceQuery = Order::where('isDeleted', 0)
            ->where('quotation_status', 'rental');

        if ($role !== 'admin') {
            $invoiceQuery->where('branch_id', $branchId);
        }

        $invoiceNumbers = $invoiceQuery
            ->where(function ($q) {
                $q->whereHas('user', function ($query) {
                    $query->where('isDeleted', 0);
                })->orWhereNull('user_id');
            })
            ->whereHas('orderItems', function ($query) {
                $query->whereRaw('order_items.quantity > (SELECT IFNULL(SUM(qty_returned), 0) FROM rental_return_items WHERE rental_return_items.order_item_id = order_items.id)');
            })
            ->orderBy('id', 'desc')
            ->pluck('order_number');

        $banks = \App\Models\BankMaster::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('isDeleted', 0)
            ->get();

        return view('rentalreturn.createrentalreturn', compact('invoiceNumbers', 'banks'));
    }

    public function getRentalDetails(Request $request, $invoiceNumber)
    {
        $user          = Auth::guard('api')->user();
        $role          = $user->role;
        $userBranchId  = $user->id;
        $BranchId      = $user->branch_id;
        $branchIdToUse = $BranchId;

        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userBranchId;

        if ($role === 'admin' && $selectedSubAdminId) {
            $branchIdToUse = $selectedSubAdminId;
        } elseif ($role === 'sub-admin') {
            $branchIdToUse = $userBranchId; // sub-admin branch
        }

        $order = Order::where('order_number', $invoiceNumber)->first();

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $user = User::find($order->user_id);

        $taxIdsRaw = $order->tax_id;
        $taxIds = is_array($taxIdsRaw) ? $taxIdsRaw : (json_decode($taxIdsRaw ?? '[]', true) ?: []);

        $taxes = TaxRate::whereIn('id', $taxIds)
            ->where('branch_id', $branchIdToUse)
            ->where('isDeleted', 0)
            ->get();

        $items = OrderItem::where('order_id', $order->id)->get()->map(function ($item) {
            $product   = Product::find($item->product_id);
            $images    = json_decode($product->images ?? '[]', true);
            $imageUrls = $product->image_url ?? [];

            $returnedQty = RentalReturnItem::where('order_item_id', $item->id)->sum('qty_returned');
            $availableQty = max(0, $item->quantity - $returnedQty);

            // Fetch rent period from item
            $rentPeriod = ($item->rent_value && $item->rent_period) ? ($item->rent_value . ' ' . $item->rent_period) : 'N/A';

            return [
                'id'                  => $item->id,
                'product_id'          => $item->product_id,
                'product_name'        => $product->name ?? 'N/A',
                'product_image'       => $images[0] ?? null,
                'product_image_url'   => $imageUrls[0] ?? null,
                'quantity'            => $availableQty,
                'sold_quantity'       => $item->quantity,
                'returned_quantity'   => $returnedQty,
                'price'               => $item->price,
                'base_price_db'       => $item->price,
                'discount_percentage' => $item->discount_percentage,
                'discount_amount'     => $item->discount_amount,
                'product_gst_details' => $item->product_gst_details,
                'product_gst_total'   => $item->product_gst_total,
                'total_amount'        => $item->total_amount,
                'rent_period'         => $rentPeriod,
                'rent_start_at'       => $item->rent_start_at,
                'rent_end_at'         => $item->rent_end_at,
                'rent_value'          => $item->rent_value,
                'raw_rent_period'     => $item->rent_period,
            ];
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        })->values();

        $actualPaidAmount = PaymentStore::where('order_id', $order->id)
            ->where('isDeleted', 0)
            ->sum('payment_amount');

        $otherChargesRaw = $order->other_charges;
        $otherChargesTotal = 0;
        if (is_numeric($otherChargesRaw)) {
            $otherChargesTotal = (float)$otherChargesRaw;
        } else if (is_array($otherChargesRaw)) {
            foreach ($otherChargesRaw as $charge) {
                $otherChargesTotal += isset($charge['amount']) ? (float)$charge['amount'] : 0;
            }
        } else if (is_string($otherChargesRaw)) {
            $decoded = json_decode($otherChargesRaw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $charge) {
                    $otherChargesTotal += isset($charge['amount']) ? (float)$charge['amount'] : 0;
                }
            }
        }

        $orderData = [
            'id'               => $order->id,
            'user_id'          => $order->user_id,
            'order_number'     => $order->order_number,
            'user_name'        => $user->name ?? 'N/A',
            'user_phone'       => $user->phone ?? '',
            'discount'         => $order->discount ?? 0,
            'shipping'         => $order->shipping ?? 0,
            'tds_amount'       => $order->tds_amount ?? 0,
            'other_charges'    => $otherChargesTotal,
            'deposit_amount'   => $order->deposit_amount ?? 0,
            'deposit_status'   => $order->deposit_status ?? 'held',
            'total_amount'     => $order->total_amount ?? 0,
            'remaining_amount' => $order->remaining_amount ?? 0,
            'paid_amount'      => (float) $actualPaidAmount,
        ];

        $settings         = \DB::table('settings')->where('branch_id', $selectedSubAdminId)->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'order'             => $orderData,
            'items'             => $items,
            'taxes'             => $taxes,
            'currency_symbol'   => $currencySymbol,
            'currency_position' => $currencyPosition,
        ]);
    }

    public function return_rental(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|integer',
            'payment_method' => 'required|string',
            'grand_total'    => 'required|numeric',
            'entered_amount' => 'required|numeric',
            'is_full_return' => 'nullable|boolean',
            'products'       => 'required|array',
            'products.*.order_item_id' => 'required|integer',
            'products.*.quantity'      => 'required|numeric|min:0',
            'products.*.price'         => 'required|numeric',
            'products.*.subtotal'      => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            $user  = auth('api')->user();

            // ── Adjust OrderItem price/duration if modified on return page ────
            $orderTotalAdjustment = 0;
            $orderTaxAdjustment = 0;
            $orderSubtotalAdjustment = 0;

            foreach ($request->products as $item) {
                if (isset($item['rent_value']) && isset($item['price'])) {
                    $orderItem = OrderItem::find($item['order_item_id']);
                    if ($orderItem) {
                        $oldBasePrice = (float) $orderItem->price;
                        $newBasePrice = (float) $item['price'];
                        $oldRentValue = (float) $orderItem->rent_value;
                        $newRentValue = isset($item['rent_value']) ? (float) $item['rent_value'] : $oldRentValue;
                        
                        $oldSubtotal = $oldBasePrice * $oldRentValue * $orderItem->quantity;
                        $newSubtotal = $newBasePrice * $newRentValue * $orderItem->quantity;
                        
                        $oldGstTotal = (float) $orderItem->product_gst_total;
                        $newGstTotal = $oldGstTotal * ($oldRentValue > 0 ? ($newRentValue / $oldRentValue) : 1);
                        
                        $differenceInSubtotal = $newSubtotal - $oldSubtotal;
                        $differenceInGst = $newGstTotal - $oldGstTotal;
                        
                        if (abs($differenceInSubtotal) > 0.01 || abs($differenceInGst) > 0.01) {
                            $orderItem->price = $newBasePrice;
                            $orderItem->product_gst_total = $newGstTotal;
                            
                            if (isset($item['rent_value'])) {
                                $orderItem->rent_value = $item['rent_value'];
                            }
                            if (isset($item['rent_end_at']) && !empty($item['rent_end_at'])) {
                                $orderItem->rent_end_at = $item['rent_end_at'];
                            }
                            $orderItem->save();
                            
                            $orderSubtotalAdjustment += $differenceInSubtotal;
                            $orderTaxAdjustment += $differenceInGst;
                            $orderTotalAdjustment += ($differenceInSubtotal + $differenceInGst);
                        }
                    }
                }
            }

            if ($orderTotalAdjustment != 0) {
                $order->total_amount = (float) $order->total_amount + $orderTotalAdjustment;
                $order->save();
            }

            // ── Determine if this is a full order return ──────────────────
            // A full return means every order item's remaining qty is being
            // returned completely in this batch.
            $isFullReturn = false;
            $allOrderItems = OrderItem::where('order_id', $order->id)->get();

            // Build a map of qty being returned in this request
            $returningQtyMap = [];
            foreach ($request->products as $item) {
                $returningQtyMap[$item['order_item_id']] = (float) $item['quantity'];
            }

            $allFullyReturned = true;
            foreach ($allOrderItems as $orderItem) {
                $alreadyReturned = RentalReturnItem::where('order_item_id', $orderItem->id)
                    ->sum('qty_returned');
                $remainingQty    = max(0, $orderItem->quantity - $alreadyReturned);
                $returningNow    = $returningQtyMap[$orderItem->id] ?? 0;

                if ($returningNow < $remainingQty) {
                    $allFullyReturned = false;
                    break;
                }
            }
            $isFullReturn = $allFullyReturned;

            // ── Deposit settlement on full return ─────────────────────────
            $depositAmount        = (float) ($order->deposit_amount ?? 0);
            $depositSettled       = false;
            $depositAmountApplied = 0;

            if ($isFullReturn && $depositAmount > 0) {
                $depositSettled       = true;
                $depositAmountApplied = $depositAmount;
            }

            // ── Validate: full return requires full payment ───────────────
            if ($isFullReturn) {
                $actualPaid    = PaymentStore::where('order_id', $order->id)
                                    ->where('isDeleted', 0)->sum('payment_amount');
                $totalOwed     = (float) $order->total_amount - (float) $actualPaid - $depositAmountApplied;
                if ($totalOwed < 0) $totalOwed = 0;

                if ((float) $request->entered_amount < $totalOwed - 0.01) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Full return requires full payment of ₹' . number_format($totalOwed, 2) . '. Please collect the complete amount.',
                    ], 422);
                }
            }

            // ── Create Rental Return record ───────────────────────────────
            $rentalReturn = new RentalReturn();
            $rentalReturn->order_id                 = $order->id;
            $rentalReturn->branch_id                = $order->branch_id;
            $rentalReturn->returned_by              = $user->id;
            $rentalReturn->return_date              = date('Y-m-d');
            $rentalReturn->total_late_fee           = 0;
            $rentalReturn->total_damage_charge      = 0;
            $rentalReturn->final_settlement_amount  = $request->grand_total;
            $rentalReturn->payment_method           = $request->payment_method;
            $rentalReturn->refund_amount            = 0;
            $rentalReturn->remarks                  = $request->reviews ?? '';
            $rentalReturn->is_full_return           = $isFullReturn ? 1 : 0;
            $rentalReturn->deposit_settled          = $depositSettled ? 1 : 0;
            $rentalReturn->deposit_amount_applied   = $depositAmountApplied;
            $rentalReturn->save();

            // ── Process each returned item ────────────────────────────────
            foreach ($request->products as $item) {
                if ((float) $item['quantity'] > 0) {
                    $orderItem = OrderItem::find($item['order_item_id']);
                    if ($orderItem) {
                        $returnItem                   = new RentalReturnItem();
                        $returnItem->rental_return_id = $rentalReturn->id;
                        $returnItem->order_item_id    = $orderItem->id;
                        $returnItem->product_id       = $orderItem->product_id;
                        $returnItem->variant_id       = 0;
                        $returnItem->qty_returned     = $item['quantity'];
                        $returnItem->condition        = 'good';
                        $returnItem->damage_charge    = 0;
                        $returnItem->late_fee         = 0;
                        $returnItem->save();

                        // ── Restore inventory (product + variant + log) ───────
                        $product = Product::find($orderItem->product_id);
                        if ($product) {
                            $oldStock = (float) $product->quantity;

                            // Restore main product stock
                            $product->quantity = $oldStock + (float) $item['quantity'];
                            $product->save();

                            // Restore variant stock if applicable
                            if (!empty($orderItem->product_variant_id)) {
                                $variantObj = \App\Models\ProductVariant::find($orderItem->product_variant_id);
                                if ($variantObj) {
                                    $variantObj->increment('qty', (float) $item['quantity']);
                                }
                            }

                            // Log to ProductInventory (mirrors sales return behaviour)
                            ProductInventory::create([
                                'product_id'         => $product->id,
                                'product_variant_id' => $orderItem->product_variant_id ?: null,
                                'initial_stock'      => $oldStock,
                                'current_stock'      => $product->quantity,
                                'type'               => 'Rental Return',
                                'branch_id'          => $product->branch_id,
                                'create_by'          => $user->id,
                                'date'               => now(),
                            ]);
                        }
                    }
                }
            }

            // ── Record payment if entered_amount > 0 ─────────────────────
            if ((float) $request->entered_amount > 0) {
                $paymentMethodForStore = $request->payment_method;
                $paidType = $request->paid_type ?? 'fully';
                $bankId = $request->bank_id ?? null;
                $cashAmount = (float) ($request->cash_amount ?? 0);
                $onlineAmount = (float) ($request->online_amount ?? 0);
                $enteredAmount = (float) $request->entered_amount;
                $remarks = $request->remark ?? $request->reviews ?? '';
                $remain = max(0, (float) $order->remaining_amount - $enteredAmount);

                if ($paymentMethodForStore === 'cash+online') {
                    $cashToStore = 0.0;
                    $onlineToStore = 0.0;

                    if (($cashAmount + $onlineAmount) > 0) {
                        $cashToStore = min($cashAmount, $enteredAmount);
                        $onlineToStore = min($onlineAmount, max($enteredAmount - $cashToStore, 0));

                        $remainingSplit = max($enteredAmount - ($cashToStore + $onlineToStore), 0);
                        if ($remainingSplit > 0) {
                            $onlineToStore += $remainingSplit;
                        }
                    } else {
                        $onlineToStore = $enteredAmount;
                    }

                    if ($cashToStore > 0) {
                        PaymentStore::create([
                            'order_id' => $order->id,
                            'payment_amount' => $cashToStore,
                            'payment_date' => now(),
                            'payment_method' => 'cash',
                            'payment_type' => $paidType,
                            'cash_amount' => $cashToStore,
                            'upi_amount' => 0,
                            'remaining_amount' => $remain,
                            'bank_id' => $bankId,
                            'user_id' => $order->user_id,
                            'status' => 'credit',
                            'remarks' => $remarks,
                        ]);
                    }

                    if ($onlineToStore > 0) {
                        PaymentStore::create([
                            'order_id' => $order->id,
                            'payment_amount' => $onlineToStore,
                            'payment_date' => now(),
                            'payment_method' => 'online',
                            'payment_type' => $paidType,
                            'cash_amount' => 0,
                            'upi_amount' => $onlineToStore,
                            'remaining_amount' => $remain,
                            'bank_id' => $bankId,
                            'user_id' => $order->user_id,
                            'status' => 'credit',
                            'remarks' => $remarks,
                        ]);
                    }
                } else {
                    $paymentStoreData = [
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'payment_amount' => $enteredAmount,
                        'payment_date' => now(),
                        'payment_method' => $paymentMethodForStore,
                        'payment_type' => $paidType,
                        'cash_amount' => $paymentMethodForStore === 'cash' ? $enteredAmount : 0,
                        'upi_amount' => ($paymentMethodForStore === 'online' || $paymentMethodForStore === 'scan' || $paymentMethodForStore === 'debit card') ? $enteredAmount : 0,
                        'remaining_amount' => $remain,
                        'bank_id' => $bankId,
                        'status' => 'credit',
                        'remarks' => $remarks,
                    ];

                    if ($paymentMethodForStore === 'emi') {
                        $paymentStoreData['emi_down_payment']   = $request->emi_down_payment;
                        $paymentStoreData['emi_loan_amount']    = $request->emi_loan_amount;
                        $paymentStoreData['emi_interest_rate']  = $request->emi_interest_rate;
                        $paymentStoreData['emi_tenure']         = $request->emi_tenure;
                        $paymentStoreData['emi_monthly_amount'] = $request->emi_monthly_amount;
                        $paymentStoreData['emi_aadhar_number']  = $request->emi_aadhar_number;
                        $paymentStoreData['emi_do_id']          = $request->emi_do_id;
                        $paymentStoreData['emi_pan_number']     = $request->emi_pan_number;
                        $paymentStoreData['emi_guarantor_name'] = $request->emi_guarantor_name;
                    }

                    PaymentStore::create($paymentStoreData);
                }

                // Reduce remaining_amount (no paid_amount column on orders)
                $order->remaining_amount = $remain;
            }

            // ── Settle deposit on full return ─────────────────────────────
            if ($depositSettled) {
                $order->deposit_status   = 'settled';
                $order->remaining_amount = 0; // deposit + payment covers everything
            }

            // ── Mark order return_status ──────────────────────────────────
            $order->return_status = $isFullReturn ? 'fully_returned' : 'partially_returned';

            $order->save();

            DB::commit();

            return response()->json([
                'success'        => true,
                'is_full_return' => $isFullReturn,
                'deposit_settled'=> $depositSettled,
                'message'        => $isFullReturn
                    ? 'Rental fully returned and settled successfully.'
                    : 'Rental partially returned successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function rental_return_pdf($id)
    {
        $user = auth()->user();

        // Load return with items and their products
        $return = \App\Models\RentalReturn::with(['items.product', 'order.user.userDetail'])->findOrFail($id);

        $order = $return->order;
        
        $customer = null;
        if ($order && $order->user) {
            $customer = [
                'name'             => $order->user->name ?? 'Default-customer',
                'company_name'     => $order->user->company_name ?? '',
                'email'            => $order->user->email ?? '',
                'phone'            => $order->user->phone ?? '',
                'address'          => optional($order->user->userDetail)->address ?? '',
                'delivery_address' => optional($order->user->userDetail)->delivery_address ?? '',
                'gst_number'       => $order->user->gst_number ?? '',
                'pan_number'       => $order->user->pan_number ?? '',
            ];
        }

        // Determine branch for settings
        if ($user->role === 'staff' && $user->branch_id) {
            $branchId = $user->branch_id;
        } elseif ($user->role === 'admin') {
            $branchId = $user->id;
        } else {
            $branchId = $user->id;
        }

        $setting = \DB::table('settings')->where('branch_id', $branchId)->first();

        if (!$setting) {
            $setting = \App\Models\Setting::first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rentalreturn.rental-return-pdf', compact('return', 'customer', 'setting'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('rental_return_' . $return->id . '.pdf');
    }
}
