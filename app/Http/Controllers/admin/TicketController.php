<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\BrandTicketCreatedMail;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketStatusUpdatedMail;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Services\MailConfigService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    private const PRIORITIES = ['low', 'medium', 'high', 'urgent'];
    private const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];

    private function resolveBranchId(): int
    {
        $user               = Auth::user();
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($user->role === 'staff' && ! empty($user->branch_id)) {
            return (int) $user->branch_id;
        }

        if ($user->role === 'admin' && ! empty($selectedSubAdminId)) {
            return (int) $selectedSubAdminId;
        }

        return (int) ($user->branch_id ?? $user->id);
    }

    private function branchCustomers(int $branchId)
    {
        return User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();
    }

    private function branchStaff(int $branchId)
    {
        return User::where('role', 'staff')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('name')
            ->get();
    }

    private function ticketQuery(int $branchId, Request $request)
    {
        $query = Ticket::with(['branch', 'customer', 'assignedTo', 'assignedBy'])
            ->where('branch_id', $branchId)
            ->where('is_deleted', 0);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignedTo', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        return $query;
    }

    private function applyStatusTimestamps(Ticket $ticket, string $status): void
    {
        if (in_array($status, ['resolved', 'closed'], true) && empty($ticket->resolved_at)) {
            $ticket->resolved_at = now();
        }

        if ($status === 'closed' && empty($ticket->closed_at)) {
            $ticket->closed_at = now();
        }

        if (! in_array($status, ['resolved', 'closed'], true)) {
            $ticket->resolved_at = null;
        }

        if ($status !== 'closed') {
            $ticket->closed_at = null;
        }
    }

    private function notifyBrandForCreatedTicket(Ticket $ticket, int $branchId): void
    {
        $ticket->loadMissing(['customer', 'order', 'product.brand']);

        $brand = optional($ticket->product)->brand;
        $brandEmail = trim((string) ($brand->email ?? ''));

        if (! $brand || $brandEmail === '' || ! filter_var($brandEmail, FILTER_VALIDATE_EMAIL)) {
            Log::info("Ticket brand email skipped for ticket #{$ticket->ticket_no}: brand email not found.");
            return;
        }

        $setting = \App\Models\Setting::where('branch_id', $branchId)->first();
        $sendMailEnabled = is_null($setting?->send_mail) ? true : (bool) $setting->send_mail;

        if (! $sendMailEnabled) {
            Log::info("Ticket brand email skipped for ticket #{$ticket->ticket_no}: send mail disabled for branch {$branchId}.");
            return;
        }

        try {
            MailConfigService::setSMTP($branchId);
        } catch (\Throwable $e) {
            Log::warning(
                "SMTP setup failed for ticket #{$ticket->ticket_no} (branch: {$branchId}): "
                    . $e->getMessage()
            );
        }

        try {
            Mail::to($brandEmail)->send(new BrandTicketCreatedMail($ticket, $brand));
            Log::info("Ticket brand email sent for ticket #{$ticket->ticket_no} to {$brandEmail}.");
        } catch (\Throwable $e) {
            Log::error('Failed to send ticket brand email: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'product_id' => $ticket->product_id,
            ]);
        }
    }

    private function isTicketMailEnabled(int $branchId): bool
    {
        $setting = \App\Models\Setting::where('branch_id', $branchId)->first();

        return is_null($setting?->send_mail) ? true : (bool) $setting->send_mail;
    }

    private function prepareTicketMailer(int $branchId, string $ticketNo): bool
    {
        if (! $this->isTicketMailEnabled($branchId)) {
            Log::info("Ticket email skipped for ticket #{$ticketNo}: send mail disabled for branch {$branchId}.");
            return false;
        }

        try {
            MailConfigService::setSMTP($branchId);
        } catch (\Throwable $e) {
            Log::warning(
                "SMTP setup failed for ticket #{$ticketNo} (branch: {$branchId}): "
                    . $e->getMessage()
            );
        }

        return true;
    }

    private function notifyTicketStatusUpdated(Ticket $ticket, int $branchId, string $oldStatus): void
    {
        $ticket->loadMissing(['customer', 'order', 'product.brand']);

        if (! $this->prepareTicketMailer($branchId, (string) $ticket->ticket_no)) {
            return;
        }

        $customer = $ticket->customer;
        $customerEmail = trim((string) ($customer->email ?? ''));

        if ($customer && $customerEmail !== '' && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($customerEmail)->send(
                    new TicketStatusUpdatedMail($ticket, $oldStatus, $customer->name ?? 'Customer', 'customer')
                );
                Log::info("Ticket status email sent for ticket #{$ticket->ticket_no} to customer {$customerEmail}.");
            } catch (\Throwable $e) {
                Log::error('Failed to send ticket status email to customer: ' . $e->getMessage(), [
                    'ticket_id' => $ticket->id,
                    'customer_id' => $ticket->customer_id,
                ]);
            }
        } else {
            Log::info("Ticket status email skipped for ticket #{$ticket->ticket_no}: customer email not found.");
        }

        $brand = optional($ticket->product)->brand;
        $brandEmail = trim((string) ($brand->email ?? ''));

        if ($brand && $brandEmail !== '' && filter_var($brandEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($brandEmail)->send(
                    new TicketStatusUpdatedMail($ticket, $oldStatus, $brand->name ?? 'Brand Team', 'brand')
                );
                Log::info("Ticket status email sent for ticket #{$ticket->ticket_no} to brand {$brandEmail}.");
            } catch (\Throwable $e) {
                Log::error('Failed to send ticket status email to brand: ' . $e->getMessage(), [
                    'ticket_id' => $ticket->id,
                    'product_id' => $ticket->product_id,
                    'brand_id' => $brand->id,
                ]);
            }
        } else {
            Log::info("Ticket status email skipped for ticket #{$ticket->ticket_no}: brand email not found.");
        }
    }

    public function index(Request $request)
    {
        $branchId = $this->resolveBranchId();

        $tickets = $this->ticketQuery($branchId, $request)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = self::STATUSES;

        return view('ticket.index', compact('tickets', 'statuses'));
    }

    public function create_ticket(Request $request)
    {
        $branchId      = $this->resolveBranchId();
        $customers     = $this->branchCustomers($branchId);
        $orders        = Order::where('branch_id', $branchId)->where('isDeleted', 0)->orderByDesc('id')->get();
        $staffMembers  = $this->branchStaff($branchId);
        $priorities    = self::PRIORITIES;
        $statuses      = self::STATUSES;
        $preselectedCustomerId = '';
        $preselectedOrderId    = '';

        if ($request->filled('order_id')) {
            $preselectedOrder = Order::where('branch_id', $branchId)
                ->where('isDeleted', 0)
                ->find($request->order_id);

            if ($preselectedOrder) {
                $preselectedOrderId    = $preselectedOrder->id;
                $preselectedCustomerId = $preselectedOrder->user_id;
            }
        }

        if ($request->filled('customer_id') && empty($preselectedCustomerId)) {
            $preselectedCustomer = User::where('role', 'customer')
                ->where('branch_id', $branchId)
                ->where('isDeleted', 0)
                ->find($request->customer_id);

            if ($preselectedCustomer) {
                $preselectedCustomerId = $preselectedCustomer->id;
            }
        }

        return view('ticket.create', compact('customers', 'orders', 'staffMembers', 'priorities', 'statuses', 'preselectedCustomerId', 'preselectedOrderId'));
    }

    public function store_ticket(Request $request)
    {
        $branchId = $this->resolveBranchId();

        $validated = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($branchId) {
                    $query->where('role', 'customer')
                        ->where('branch_id', $branchId)
                        ->where('isDeleted', 0);
                }),
            ],
            'order_id' => ['required', 'exists:orders,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'ticket_raised_in' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable','string'],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'status' => ['required', Rule::in(self::STATUSES)],

            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $raisedIn = [];
        if ($request->has('send_email') && $request->send_email == 1) {
            $raisedIn[] = 'Email';
        }
        if ($request->has('send_whatsapp') && $request->send_whatsapp == 1) {
            $raisedIn[] = 'WhatsApp';
        }
        $ticketRaisedInValue = count($raisedIn) > 0 ? implode(', ', $raisedIn) : null;

        $ticket = Ticket::create([
            'branch_id'   => $branchId,
            'customer_id' => $validated['customer_id'],
            'order_id'    => $validated['order_id'] ?? null,
            'product_id'  => $validated['product_id'] ?? null,
            'ticket_raised_in' => $ticketRaisedInValue,
            'subject'     => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'priority'    => $validated['priority'],
            'status'      => $validated['status'],
            'remarks'     => $validated['remarks'] ?? null,
            'attachment'  => $attachmentPath,
            'is_deleted'  => 0,
        ]);

        $ticket->ticket_no = 'TKT-' . str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT);
        $this->applyStatusTimestamps($ticket, $ticket->status);
        $ticket->save();

        $customer = User::find($ticket->customer_id);
        $this->notifyBrandForCreatedTicket($ticket, $branchId);

        if ($request->has('send_email') && $request->send_email == 1 && $customer && $customer->email) {
            try {
                MailConfigService::setSMTP($branchId);
                Mail::to($customer->email)->send(new TicketCreatedMail($ticket, $customer));
            } catch (\Exception $e) {
                Log::error("Failed to send ticket email: " . $e->getMessage());
            }
        }

        if ($request->has('send_whatsapp') && $request->send_whatsapp == 1 && $customer && $customer->phone) {
            $setting = \App\Models\Setting::where('branch_id', $branchId)->first();
            if ($setting && $setting->customer_whatsapp_message) {
                try {
                    $orderNo = $ticket->order ? $ticket->order->order_number : 'N/A';
                    $productName = $ticket->product ? ($ticket->product->name ?? $ticket->product->product_name ?? 'Product #' . $ticket->product_id) : 'N/A';
                    $date = $ticket->created_at ? $ticket->created_at->format('d-m-Y') : date('d-m-Y');
                    WhatsAppService::sendTicketNotification($branchId, $customer->phone, $customer->name, $ticket->ticket_no, $orderNo, $productName, $ticket->subject, $ticket->priority, $date);
                } catch (\Exception $e) {
                    Log::error("Failed to send ticket whatsapp: " . $e->getMessage());
                }
            }
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Ticket created successfully.',
                'redirect' => route('ticket.list'),
            ]);
        }

        return redirect()->route('ticket.list')->with('success', 'Ticket created successfully.');
    }

    public function view_ticket($id)
    {
        $branchId = $this->resolveBranchId();
        $ticket   = Ticket::with(['customer', 'assignedTo', 'assignedBy'])
            ->where('branch_id', $branchId)
            ->where('is_deleted', 0)
            ->findOrFail($id);

        return view('ticket.view', compact('ticket'));
    }

    public function customer_history($customerId)
    {
        $branchId = $this->resolveBranchId();
        $customer = User::where('id', $customerId)
            ->where('role', 'customer')
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->firstOrFail();

        $tickets = Ticket::with(['assignedTo'])
            ->where('branch_id', $branchId)
            ->where('customer_id', $customerId)
            ->where('is_deleted', 0)
            ->orderByDesc('id')
            ->get();

        return view('ticket.history', compact('customer', 'tickets'));
    }

    public function edit_ticket($id)
    {
        $branchId     = $this->resolveBranchId();
        $ticket       = Ticket::where('branch_id', $branchId)->where('is_deleted', 0)->findOrFail($id);
        $customers    = $this->branchCustomers($branchId);
        $orders       = Order::where('branch_id', $branchId)->where('isDeleted', 0)->orderByDesc('id')->get();
        $staffMembers = $this->branchStaff($branchId);
        $priorities   = self::PRIORITIES;
        $statuses     = self::STATUSES;

        return view('ticket.edit', compact('ticket', 'customers', 'orders', 'staffMembers', 'priorities', 'statuses'));
    }

    public function update_ticket(Request $request, $id)
    {
        $branchId = $this->resolveBranchId();
        $ticket   = Ticket::where('branch_id', $branchId)->where('is_deleted', 0)->findOrFail($id);
        $oldStatus = (string) $ticket->status;

        $validated = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($branchId) {
                    $query->where('role', 'customer')
                        ->where('branch_id', $branchId)
                        ->where('isDeleted', 0);
                }),
            ],
            'order_id' => ['nullable', 'exists:orders,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'ticket_raised_in' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'status' => ['required', Rule::in(self::STATUSES)],

            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        if ($request->hasFile('attachment')) {
            if (! empty($ticket->attachment)) {
                Storage::disk('public')->delete($ticket->attachment);
            }
            $ticket->attachment = $request->file('attachment')->store('tickets', 'public');
        }

        $raisedIn = [];
        if ($request->has('send_email') && $request->send_email == 1) {
            $raisedIn[] = 'Email';
        }
        if ($request->has('send_whatsapp') && $request->send_whatsapp == 1) {
            $raisedIn[] = 'WhatsApp';
        }
        $ticketRaisedInValue = count($raisedIn) > 0 ? implode(', ', $raisedIn) : $ticket->ticket_raised_in;

        $ticket->customer_id = $validated['customer_id'];
        $ticket->order_id    = $validated['order_id'] ?? null;
        $ticket->product_id  = $validated['product_id'] ?? null;
        $ticket->ticket_raised_in = $ticketRaisedInValue;
        $ticket->subject     = $validated['subject'];
        $ticket->description = $validated['description'] ?? null;
        $ticket->priority    = $validated['priority'];
        $ticket->status      = $validated['status'];
        $ticket->remarks     = $validated['remarks'] ?? null;

        $this->applyStatusTimestamps($ticket, $ticket->status);
        $ticket->save();

        $customer = User::find($ticket->customer_id);

        if ($oldStatus !== (string) $ticket->status) {
            $this->notifyTicketStatusUpdated($ticket, $branchId, $oldStatus);
        }

        if ($request->has('send_email') && $request->send_email == 1 && $customer && $customer->email) {
            try {
                MailConfigService::setSMTP($branchId);
                Mail::to($customer->email)->send(new TicketCreatedMail($ticket, $customer));
            } catch (\Exception $e) {
                Log::error("Failed to send ticket email: " . $e->getMessage());
            }
        }

        if ($request->has('send_whatsapp') && $request->send_whatsapp == 1 && $customer && $customer->phone) {
            $setting = \App\Models\Setting::where('branch_id', $branchId)->first();
            if ($setting && $setting->customer_whatsapp_message) {
                try {
                    $orderNo = $ticket->order ? $ticket->order->order_number : 'N/A';
                    $productName = $ticket->product ? ($ticket->product->name ?? $ticket->product->product_name ?? 'Product #' . $ticket->product_id) : 'N/A';
                    $date = $ticket->updated_at ? $ticket->updated_at->format('d-m-Y') : date('d-m-Y');
                    WhatsAppService::sendTicketNotification($branchId, $customer->phone, $customer->name, $ticket->ticket_no, $orderNo, $productName, $ticket->subject, $ticket->priority, $date);
                } catch (\Exception $e) {
                    Log::error("Failed to send ticket whatsapp: " . $e->getMessage());
                }
            }
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Ticket updated successfully.',
                'redirect' => route('ticket.list'),
            ]);
        }

        return redirect()->route('ticket.list')->with('success', 'Ticket updated successfully.');
    }

    public function destroy_ticket($id)
    {
        $branchId = $this->resolveBranchId();
        $ticket   = Ticket::where('branch_id', $branchId)->where('is_deleted', 0)->findOrFail($id);

        if (! empty($ticket->attachment)) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->is_deleted = 1;
        $ticket->save();

        return redirect()->route('ticket.list')->with('success', 'Ticket deleted successfully.');
    }

    public function get_order_products(Request $request)
    {
        $orderId = $request->order_id;
        if (!$orderId) {
            return response()->json(['status' => false, 'data' => []]);
        }

        $order = Order::with('orderItems.product')->find($orderId);
        if (!$order) {
            return response()->json(['status' => false, 'data' => []]);
        }

        $products = [];
        foreach ($order->orderItems as $item) {
            if ($item->product) {
                $images = is_string($item->product->images) ? json_decode($item->product->images, true) : $item->product->images;
                $firstImage = (is_array($images) && count($images) > 0) ? $images[0] : null;

                $products[] = [
                    'id' => $item->product->id,
                    'name' => $item->product->name ?? $item->product->product_name ?? 'Product #' . $item->product_id,
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'image' => $firstImage ? asset('public/storage/' . $firstImage) : asset('public/admin/assets/img/product/product1.jpg')
                ];
            }
        }

        return response()->json(['status' => true, 'data' => $products]);
    }

    public function get_customer_orders(Request $request)
    {
        $customerId = $request->customer_id;
        $branchId = $this->resolveBranchId();

        $query = Order::where('branch_id', $branchId)->where('isDeleted', 0);

        if ($customerId) {
            $query->where('user_id', $customerId);
        }

        $orders = $query->orderByDesc('id')->get(['id', 'order_number', 'user_id']);

        return response()->json(['status' => true, 'data' => $orders]);
    }

    public function report(Request $request)
    {
        $branchId     = $this->resolveBranchId();
        $tickets      = $this->ticketQuery($branchId, $request)->orderByDesc('id')->paginate(20)->withQueryString();
        $customers    = $this->branchCustomers($branchId);
        $staffMembers  = $this->branchStaff($branchId);
        $priorities   = self::PRIORITIES;
        $statuses     = self::STATUSES;

        return view('ticket.report', compact('tickets', 'customers', 'staffMembers', 'priorities', 'statuses'));
    }

    public function reportPdf(Request $request)
    {
        $branchId = $this->resolveBranchId();
        $setting  = \App\Models\Setting::where('branch_id', $branchId)->first()
                 ?? \App\Models\Setting::first();

        $tickets = $this->ticketQuery($branchId, $request)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($t) => [
                'ticket_no'   => $t->ticket_no ?? '-',
                'customer'    => optional($t->customer)->name ?? '-',
                'subject'     => $t->subject,
                'priority'    => ucfirst($t->priority ?? '-'),
                'status'      => ucwords(str_replace('_', ' ', $t->status ?? '-')),
                'assigned_to' => optional($t->assignedTo)->name ?? 'Unassigned',
                'assigned_by' => optional($t->assignedBy)->name ?? '-',
                'resolved_at' => optional($t->resolved_at)?->format('d-m-Y h:i A') ?? '-',
                'closed_at'   => optional($t->closed_at)?->format('d-m-Y h:i A') ?? '-',
                'created_at'  => optional($t->created_at)?->format('d-m-Y') ?? '-',
            ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ticket.report_pdf', [
                    'tickets' => $tickets,
                    'setting' => $setting,
                ])->setPaper('a4', 'landscape');

        return $pdf->stream('ticket_report_' . date('Ymd_His') . '.pdf');
    }
}
