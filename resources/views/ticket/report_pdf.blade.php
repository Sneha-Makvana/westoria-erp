<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ticket Report</title>
<style>
    @page { size: A4 landscape; margin: 6mm 8mm; }
    * { box-sizing: border-box; }

    body {
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        font-size: 11px; margin: 0; padding: 0; background: white; color: #333;
    }
    .pdf-wrapper { margin-top: 3mm; }
    .card-body {
        width: 100%; padding: 4mm;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,.1);
        border: 1px solid black;
    }

    /* ── Header ── */
    .section-divider { height: 2px; background: #d7cdcd; border: none; margin: 0 0 8px; }
    h3.section-title {
        margin: 0; font-size: 13px; font-weight: bold;
        border-left: 3px solid #b3b6b9; padding-left: 6px; color: #333;
    }

    /* ── Table ── */
    .table-style {
        width: 100%; border-collapse: collapse;
        margin-bottom: 10px; font-size: 9.5px;
        table-layout: fixed;          /* fixed layout so widths are respected */
    }
    .table-style th {
        background: #ff9f43; color: #fff; font-weight: 600;
        padding: 5px 4px; text-align: center; border: 1px solid #dee2e6;
        overflow: hidden;
    }
    .table-style td {
        border: 1px solid #dee2e6; padding: 5px 4px;
        vertical-align: middle; text-align: center;
        word-break: break-word; overflow: hidden;
    }
    .table-style td.text-left { text-align: left; }
    .table-style tr:nth-child(even) { background: #fafafa; }

    /* ── Badges ── */
    .badge {
        display: inline-block; padding: 2px 6px; border-radius: 999px;
        font-size: 8px; font-weight: bold; text-transform: uppercase;
    }
    .badge-low         { background: #dbeafe; color: #1d4ed8; }
    .badge-medium      { background: #fef3c7; color: #92400e; }
    .badge-high        { background: #ffe4e6; color: #be123c; }
    .badge-urgent      { background: #fee2e2; color: #991b1b; }
    .badge-open        { background: #dbeafe; color: #1d4ed8; }
    .badge-in_progress { background: #fef3c7; color: #92400e; }
    .badge-resolved    { background: #dcfce7; color: #15803d; }
    .badge-closed      { background: #f3f4f6; color: #4b5563; }

    /* ── Total box ── */
    .total-box { margin-top: 6px; }
    .total-box table { width: auto; border: none; font-size: 11px; }
    .total-box td    { border: none; padding: 4px 8px; }
    .total-label  { background: #f7f7f7; font-weight: bold; border-radius: 4px 0 0 4px; }
    .total-amount { background: #f0f4ff; font-weight: bold; border-radius: 0 4px 4px 0; }

    /* ── Summary row ── */
    .summary-row { font-size: 10px; color: #475569; margin-bottom: 7px; }
    .summary-row strong { color: #1e293b; }
</style>
</head>
<body>

@php
    $setting = $setting ?? new \App\Models\Setting([
        'name' => 'Company Name', 'email' => '', 'phone' => '', 'address' => '', 'logo' => null,
    ]);
    $byStatus   = $tickets->groupBy('status');
    $byPriority = $tickets->groupBy('priority');

    // Resolve correct logo path: logo is stored as "logos/file.webp"
    // which lives at public/storage/logos/file.webp
    $logoEmbedHtml = '';
    if (!empty($setting->logo)) {
        $logoPath = public_path('storage/' . $setting->logo);
        if (file_exists($logoPath)) {
            $logoData      = base64_encode(file_get_contents($logoPath));
            $logoMime      = mime_content_type($logoPath);
            $logoEmbedHtml = '<img src="data:' . $logoMime . ';base64,' . $logoData
                           . '" alt="Logo" style="height:60px;width:auto;">';
        }
    }
@endphp

<div class="pdf-wrapper">
<div class="card-body">

    {{-- ── Logo + Company header ── --}}
    <table style="width:100%; margin-bottom:8px; border-collapse:collapse;">
        <tr>
            <td style="width:140px; vertical-align:middle;">
                {!! $logoEmbedHtml !!}
            </td>
            <td style="vertical-align:middle; text-align:right; padding-left:10px;">
                <strong style="font-size:16px; text-transform:uppercase;">
                    {{ $setting->name ?? '' }}
                </strong><br>
                <span style="font-size:11px; text-transform:uppercase;">
                    {{ $setting->address ?? '' }}
                </span><br>
                <span style="font-size:11px;">
                    @if (!empty($setting->phone)) PHONE: {{ $setting->phone }} @endif
                    @if (!empty($setting->phone) && !empty($setting->email)) &nbsp;|&nbsp; @endif
                    @if (!empty($setting->email)) EMAIL: {{ $setting->email }} @endif
                </span>
            </td>
        </tr>
    </table>

    <hr class="section-divider">

    {{-- ── Section title + Date ── --}}
    <table style="width:100%; margin-bottom:6px; border-collapse:collapse;">
        <tr>
            <td style="width:50%;"><h3 class="section-title">Ticket Report</h3></td>
            <td style="text-align:right; font-weight:bold; font-size:11px; width:50%;">
                Report Date: {{ \Carbon\Carbon::now()->format('d M Y') }}
            </td>
        </tr>
    </table>

    {{-- ── Summary counts ── --}}
    <div class="summary-row">
        @foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $key => $label)
            @if ($byStatus->has($label))
                <strong>{{ $label }}</strong>: {{ $byStatus[$label]->count() }}&nbsp;&nbsp;
            @endif
        @endforeach
        &nbsp;|&nbsp;
        @foreach (['Urgent','High','Medium','Low'] as $p)
            @if ($byPriority->has($p))
                <strong>{{ $p }}</strong>: {{ $byPriority[$p]->count() }}&nbsp;&nbsp;
            @endif
        @endforeach
    </div>

    {{-- ── Tickets table ──
         Landscape A4 usable width ≈ 277mm (297mm - 2×8mm margins - border padding).
         Column budget (mm): # 8 | TicketNo 22 | Customer 28 | Subject 52 |
                              Priority 18 | Status 22 | AssignedTo 28 | Created 18 = 196mm
         Remaining 81mm spread across wider columns via flex-grow equivalent. ── --}}
    <table class="table-style">
        <colgroup>
            <col style="width:3%">   {{-- # --}}
            <col style="width:10%">  {{-- Ticket No --}}
            <col style="width:13%">  {{-- Customer --}}
            <col style="width:22%">  {{-- Subject --}}
            <col style="width:9%">   {{-- Priority --}}
            <col style="width:11%">  {{-- Status --}}
            <col style="width:13%">  {{-- Assigned To --}}
            <col style="width:10%">  {{-- Assigned By --}}
            <col style="width:9%">   {{-- Created --}}
        </colgroup>
        <thead>
            <tr>
                <th>#</th>
                <th>Ticket No</th>
                <th>Customer</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <!-- <th>Assigned To</th>
                <th>Assigned By</th> -->
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $i => $t)
                @php
                    $priorityKey = strtolower($t['priority']);
                    $statusKey   = strtolower(str_replace(' ', '_', $t['status']));
                @endphp
                <tr>
                    <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                    <td><strong>{{ $t['ticket_no'] }}</strong></td>
                    <td class="text-left">{{ $t['customer'] }}</td>
                    <td class="text-left">{{ $t['subject'] }}</td>
                    <td><span class="badge badge-{{ $priorityKey }}">{{ $t['priority'] }}</span></td>
                    <td><span class="badge badge-{{ $statusKey }}">{{ $t['status'] }}</span></td>
                    <!-- <td>{{ $t['assigned_to'] }}</td>
                    <td>{{ $t['assigned_by'] }}</td> -->
                    <td>{{ $t['created_at'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#94a3b8; padding:16px;">
                        No tickets found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Total ── --}}
    <div class="total-box">
        <table>
            <tr>
                <td class="total-label">Total Tickets</td>
                <td class="total-amount">{{ count($tickets) }}</td>
            </tr>
        </table>
    </div>

</div>
</div>
</body>
</html>
