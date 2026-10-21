@php
    $setting = \App\Models\Setting::where('branch_id', $ticket->branch_id)->first();
    $createdAt = $ticket->created_at ? $ticket->created_at->timezone('Asia/Kolkata')->format('d M Y h:i A') : '-';
    $product = $ticket->product;
    $customer = $ticket->customer;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Issue Reported - {{ $ticket->ticket_no }}</title>
</head>

<body style="margin:0; padding:0; background:#edf1f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding:24px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="760"
                    style="max-width:760px; width:100%; background:#ffffff; border:1px solid #d9e1ec; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td
                            style="background:#f7fafc; border-bottom:1px solid #d9e1ec; border-top:5px solid #ec8d2f; padding:20px 24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td valign="middle" style="width:45%; padding-right:14px;">
                                        @if (!empty($setting?->logo_url))
                                            <img src="{{ $setting->logo_url }}"
                                                alt="{{ $setting->name ?? config('app.name') }}"
                                                style="max-width:170px; height:auto; display:block;">
                                        @else
                                            <div style="font-size:18px; font-weight:700; color:#111827;">
                                                {{ $setting?->name ?? config('app.name') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td valign="middle" align="right"
                                        style="width:55%; font-size:13px; line-height:1.6; color:#334155;">
                                        <div
                                            style="display:inline-block; margin-bottom:10px; background:#ec8d2f; color:#ffffff; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:0.5px; padding:4px 12px;">
                                            Product Issue
                                        </div>
                                        <div
                                            style="font-size:14px; font-weight:700; line-height:1.2; color:#0f172a; margin-bottom:4px;">
                                            {{ $setting?->name ?? config('app.name') }}
                                        </div>
                                        @if (!empty($setting?->phone))
                                            <div><strong>Phone:</strong> {{ $setting->phone }}</div>
                                        @endif
                                        @if (!empty($setting?->email))
                                            <div><strong>Email:</strong> {{ $setting->email }}</div>
                                        @endif
                                        @if (!empty($setting?->address))
                                            <div><strong>Address:</strong> {{ $setting->address }}</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0; font-size:13px; color:#334155;">Hello {{ $brand->name ?? 'Brand Team' }},</p>
                            <p style="margin:10px 0 16px 0; font-size:13px; line-height:1.6; color:#334155;">
                                A customer support ticket has been created for a product associated with your brand.
                                Please review the issue details below.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="margin-bottom:18px; background:#f8fafc; border:1px solid #d9e1ec; border-radius:10px;">
                                <tr>
                                    <td style="padding:11px 12px; font-size:13px; color:#334155;">
                                        <strong>Ticket No:</strong> {{ $ticket->ticket_no ?? '-' }}
                                    </td>
                                    <td align="right" style="padding:11px 12px; font-size:13px; color:#334155;">
                                        <strong>Date:</strong> {{ $createdAt }}
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="font-size:14px; font-weight:700; color:#0f172a; text-align:center; margin:4px 0 8px 0; letter-spacing:0.4px; text-transform:uppercase;">
                                Product Details
                            </div>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="border:1px solid #d9e1ec; background:#ffffff; margin-bottom:18px;">
                                <tr style="background:#f8fafc;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec; width:30%;">
                                        <strong>Product</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ $product->name ?? $product->product_name ?? 'Product #'.$ticket->product_id }}
                                    </td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec;">
                                        <strong>Brand</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ $brand->name ?? '-' }}
                                    </td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec;">
                                        <strong>SKU</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ $product->SKU ?? '-' }}
                                    </td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155;">
                                        <strong>Barcode</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827;">
                                        {{ $product->barcode ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="font-size:14px; font-weight:700; color:#0f172a; text-align:center; margin:4px 0 8px 0; letter-spacing:0.4px; text-transform:uppercase;">
                                Ticket / Issue Details
                            </div>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="border:1px solid #d9e1ec; background:#ffffff;">
                                @if($ticket->order)
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec; width:30%;">
                                            <strong>Order Number</strong>
                                        </td>
                                        <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                            {{ $ticket->order->order_number }}
                                        </td>
                                    </tr>
                                @endif
                                <tr style="background:#ffffff;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec; width:30%;">
                                        <strong>Customer</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ $customer->name ?? '-' }}
                                    </td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec;">
                                        <strong>Subject</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ $ticket->subject }}
                                    </td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec;">
                                        <strong>Description</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {!! nl2br(e($ticket->description ?: '-')) !!}
                                    </td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #d9e1ec;">
                                        <strong>Priority</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827; border-bottom:1px solid #d9e1ec;">
                                        {{ ucfirst($ticket->priority ?? '-') }}
                                    </td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="padding:12px 16px; font-size:13px; color:#334155;">
                                        <strong>Remarks</strong>
                                    </td>
                                    <td style="padding:12px 16px; font-size:13px; color:#111827;">
                                        {!! nl2br(e($ticket->remarks ?: '-')) !!}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0 0; font-size:13px; color:#334155; line-height:1.6;">
                                Please use the ticket number above for any follow-up communication.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center"
                            style="background-color:#f4f4f4; padding:12px 16px; border-top:1px solid #e5e7eb;">
                            <div style="font-size:14px; font-weight:600; color:#111827;">
                                &copy; {{ date('Y') }} Copyright - <a href="https://www.fableadtech.com/" target="_blank" style="color:#111827; text-decoration:none;">{{ $setting?->name ?? config('app.name', 'Fablead Developers Technolab') }}</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
