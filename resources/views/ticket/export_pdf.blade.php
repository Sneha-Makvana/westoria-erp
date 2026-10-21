<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ticket Report</title>
<style>
    @page {
        size: A4 ;
        margin: 1mm 1mm;
    }

    * { box-sizing: border-box; }

    /* body {
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
        background: white;
        color: #333;
    } */
    body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: white;
        }


    .pdf-wrapper {
        margin-top: 3mm;
    }

    .card-body {
        width: 95%;
        min-height: 95%;
        padding: 3mm;
        margin: auto;
        box-sizing: border-box;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border: 1px solid black;
        font-size: 12px;
    }

    /* ── Header logo/company ── */
    .logo img {
        height: 50px;
        max-width: 100px;
        object-fit: contain;
    }

    .company-info-header {
        text-align: right;
        line-height: 1.4;
        font-size: 10px;
    }

    /* ── Section divider ── */
    .section-divider {
        height: 2px;
        background-color: #d7cdcd;
        border: none;
        margin: 0 0 10px 0;
    }

    /* ── Section title ── */
    h3.section-title {
        margin: 0;
        font-size: 13px;
        font-weight: bold;
        border-left: 3px solid #b3b6b9;
        padding-left: 6px;
        color: #333;
    }

    /* ── Summary row ── */
    .summary-row {
        font-size: 10px;
        color: #475569;
        margin-bottom: 8px;
    }
    .summary-row strong { color: #1e293b; }

    /* ── Main table ── */
    .table-style {
        width: 95%;
        border-collapse: collapse;
        margin-bottom: 12px;
        font-size: 11px;
    }
    .table-style th {
        background: #ff9f43;
        color: #fff;
        font-weight: 600;
        padding: 6px 8px;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    .table-style td {
        border: 1px solid #dee2e6;
        padding: 6px 8px;
        text-align: center;
        vertical-align: middle;
    }
    .table-style tr:nth-child(even) { background: #fafafa; }

    /* ── Priority / Status badges ── */
    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .badge-low      { background: #dbeafe; color: #1d4ed8; }
    .badge-medium   { background: #fef3c7; color: #92400e; }
    .badge-high     { background: #ffe4e6; color: #be123c; }
    .badge-urgent   { background: #fee2e2; color: #991b1b; }
    .badge-open        { background: #dbeafe; color: #1d4ed8; }
    .badge-in_progress { background: #fef3c7; color: #92400e; }
    .badge-resolved    { background: #dcfce7; color: #15803d; }
    .badge-closed      { background: #f3f4f6; color: #4b5563; }

    /* ── Grand Total box ── */
    .total-box {
        display: flex;
        justify-content: flex-start;
        margin-top: 6px;
    }
    .total-box table { width: auto; border: none; font-size: 11px; }
    .total-box td { border: none; padding: 4px 8px; }
    .total-label  { background: #f7f7f7; font-weight: bold; border-radius: 4px 0 0 4px; }
    .total-amount { background: #f0f4ff; font-weight: bold; color: #333; border-radius: 0 4px 4px 0; }
</style>
</head>
<body>

@php
    $setting = $setting ?? new \App\Models\Setting([
        'name'    => 'Company Name',
        'email'   => '',
        'phone'   => '',
        'address' => '',
        'logo'    => null,
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

    {{-- ── Header: Logo + Company Info ── --}}
    <table style="width:100%; margin-bottom:10px; border-collapse:collapse;">
        <tr>
            {{-- Logo --}}
            <td style="width:150px; vertical-align:middle;">
                {!! $logoEmbedHtml !!}
            </td>

            {{-- Company details (right-aligned, same as sales PDF) --}}
            <td style="vertical-align:middle; text-align:right; padding-left:15px;">
                <h3 style="margin:0; text-transform:uppercase; font-size:16px; color:#000;">
                    {{ $setting->name ?? '' }}
                </h3>
                <small style="text-transform:uppercase; font-size:12px;">
                    {{ $setting->address ?? '' }}<br>
                    @if (!empty($setting->phone))
                        PHONE: {{ $setting->phone }}
                    @endif
                    @if (!empty($setting->phone) && !empty($setting->email))
                        &nbsp;|&nbsp;
                    @endif
                    @if (!empty($setting->email))
                        EMAIL: <span style="text-transform:none;">{{ $setting->email }}</span>
                    @endif
                </small>
            </td>
        </tr>
    </table>

    <hr class="section-divider">

    {{-- ── Section title + date ── --}}
    <table style="width:100%; margin-bottom:8px;">
        <tr>
            <td style="text-align:left;">
                <h3 class="section-title">Ticket Report</h3>
            </td>
            <td style="text-align:right; font-weight:bold; font-size:11px;">
                Report Date: {{ \Carbon\Carbon::now()->format('d M Y') }}
            </td>
        </tr>
    </table>

    {{-- ── Summary counts ── --}}
    <div class="summary-row">
        @foreach (['open','in_progress','resolved','closed'] as $s)
            @if ($byStatus->has(ucwords(str_replace('_',' ',$s))))
                <span style="margin-right:12px;">
                    <strong>{{ ucwords(str_replace('_',' ',$s)) }}</strong>:
                    {{ $byStatus[ucwords(str_replace('_',' ',$s))]->count() }}
                </span>
            @endif
        @endforeach
        &nbsp;&nbsp;
        @foreach (['Urgent','High','Medium','Low'] as $p)
            @if ($byPriority->has($p))
                <span style="margin-right:12px;">
                    <strong>{{ $p }}</strong>: {{ $byPriority[$p]->count() }}
                </span>
            @endif
        @endforeach
    </div>

    {{-- ── Tickets Table ── --}}
    <table class="table-style">
        <thead>
            <tr>
                <th style="width:20px;">#</th>
                <th style="width:90px;">Ticket No</th>
                <th style="width:115px;">Customer</th>
                <th>Subject</th>
                <th style="width:65px;">Priority</th>
                <th style="width:80px;">Status</th>
                <!-- <th style="width:115px;">Assigned To</th> -->
                <th style="width:70px;">Created</th>
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
                    <td style="text-align:left;">{{ $t['customer'] }}</td>
                    <td style="text-align:left;">{{ $t['subject'] }}</td>
                    <td>
                        <span class="badge badge-{{ $priorityKey }}">{{ $t['priority'] }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $statusKey }}">{{ $t['status'] }}</span>
                    </td>
                    <!-- <td>{{ $t['assigned_to'] }}</td> -->
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

    {{-- ── Total count ── --}}
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
