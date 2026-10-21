<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Attendance — Check In / Check Out</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --in: #22c55e;
            --out: #f97316;
            --accent: #3b82f6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: var(--text);
            min-height: 100vh;
            padding: 24px;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        .header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .header h1 { font-size: 1.6rem; font-weight: 700; }
        .header p { color: var(--muted); font-size: 0.9rem; margin-top: 4px; }

        .device-badge {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 0.85rem;
        }

        .device-badge span { color: var(--muted); }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
        }

        .controls input[type="date"] {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .btn {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .btn:hover { opacity: 0.9; }

        .refresh-info {
            color: var(--muted);
            font-size: 0.85rem;
            margin-left: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 20px;
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 1rem;
        }

        .card-body { padding: 0; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }

        th {
            background: rgba(0,0,0,0.2);
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.03); }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-in { background: rgba(34,197,94,0.15); color: var(--in); }
        .badge-out { background: rgba(249,115,22,0.15); color: var(--out); }
        .badge-unknown { background: rgba(148,163,184,0.15); color: var(--muted); }

        .empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .stat-box .num { font-size: 1.8rem; font-weight: 700; }
        .stat-box .lbl { color: var(--muted); font-size: 0.8rem; margin-top: 4px; }

        .live-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--in);
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .status-banner {
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .status-banner.warn {
            background: rgba(249, 115, 22, 0.12);
            border-color: rgba(249, 115, 22, 0.35);
        }

        .status-banner .meta {
            color: var(--muted);
            font-size: 0.8rem;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Biometric Attendance</h1>
                <p>Realtime T304-Mini — Employee IDs &amp; Punch Details</p>
                <p style="font-size:0.8rem;color:#64748b;margin-top:6px;">Supports Realtime Parallel DB Export (AttendanceLogs) + /iclock/cdata push</p>
            </div>
            <div class="device-badge">
                <div><span>Device:</span> {{ $device['name'] ?? 'T304-Mini' }}</div>
                <div><span>IP:</span> {{ $device['ip'] ?? '—' }} : {{ $device['port'] ?? '—' }}</div>
                <div><span>Serial:</span> {{ $device['serial'] ?? '—' }}</div>
            </div>
        </div>

        <div class="status-banner {{ ($status['needs_reexport'] ?? false) ? 'warn' : '' }}" id="status-banner">
            <div id="status-message">{{ $status['message'] ?? 'Loading status...' }}</div>
            <div class="meta" id="status-meta">
                Total in DB: {{ $status['attendance_logs_total'] ?? 0 }}
                | Today ({{ $status['calendar_today'] ?? $calendarToday }}): {{ $status['attendance_logs_today'] ?? 0 }}
                | Selected date: {{ $date }}
                @if(!empty($status['last_punch_at']))
                | Last punch: {{ $status['last_punch_at'] }}
                @endif
            </div>
            @if($status['needs_reexport'] ?? false)
            <div class="meta" style="margin-top:8px;color:#fbbf24;">
                New punches are not automatic. In Realtime software: <strong>Data Transfer</strong> → download from device → <strong>Manual Export</strong> (set To Date = today).
            </div>
            @endif
        </div>

        <div class="stats" id="stats">
            <div class="stat-box">
                <div class="num" id="stat-total">{{ count($summary) }}</div>
                <div class="lbl">Employees Today</div>
            </div>
            <div class="stat-box">
                <div class="num" id="stat-in">{{ collect($summary)->filter(fn($s) => $s['check_in'])->count() }}</div>
                <div class="lbl">Checked In</div>
            </div>
            <div class="stat-box">
                <div class="num" id="stat-out">{{ collect($summary)->filter(fn($s) => $s['check_out'])->count() }}</div>
                <div class="lbl">Checked Out</div>
            </div>
        </div>

        <div class="controls">
            <input type="date" id="date-filter" value="{{ $date }}">
            <button class="btn" onclick="setToday()">Today</button>
            <button class="btn" onclick="loadData()">Apply</button>
            <button class="btn" onclick="showAll()" style="background:#16a34a;">Show All</button>
            <button class="btn" onclick="loadData()" style="background:#475569;">Refresh Now</button>
            <span class="refresh-info">
                <span class="live-dot"></span>
                Auto-refresh every {{ $refreshSeconds }}s
            </span>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">Today's Summary (Check-In / Check-Out)</div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>In</th>
                                <th>Out</th>
                            </tr>
                        </thead>
                        <tbody id="summary-body">
                            @forelse($summary as $row)
                                <tr>
                                    <td><strong>{{ $row['device_user_id'] }}</strong></td>
                                    <td>{{ $row['employee_name'] }}</td>
                                    <td>
                                        @if($row['check_in'])
                                            <span class="badge badge-in">{{ $row['check_in'] }}</span>
                                        @else
                                            <span class="badge badge-unknown">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['check_out'])
                                            <span class="badge badge-out">{{ $row['check_out'] }}</span>
                                        @else
                                            <span class="badge badge-unknown">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty">No punches recorded for this date yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">All Punch Logs</div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Time</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody id="punches-body">
                            <tr><td colspan="4" class="empty">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const refreshSeconds = {{ (int) $refreshSeconds }};
        const apiUrl = @json(route('biometric.public.data'));

        function badgeClass(type) {
            if (type === 'check_in') return 'badge-in';
            if (type === 'check_out') return 'badge-out';
            return 'badge-unknown';
        }

        function badgeLabel(type) {
            if (type === 'check_in') return 'Check In';
            if (type === 'check_out') return 'Check Out';
            return 'Unknown';
        }

        const calendarToday = @json($calendarToday ?? now()->toDateString());

        function setToday() {
            document.getElementById('date-filter').value = calendarToday;
            loadData();
        }

        function showAll() {
            fetch(apiUrl + '?date=all')
                .then(r => r.json())
                .then(renderData)
                .catch(() => {});
        }

        function renderData(data) {
            const summary = data.summary || [];
            const status = data.status || {};

            if (status.message) {
                document.getElementById('status-message').textContent = status.message;
                let meta = `Total in DB: ${status.attendance_logs_total || 0} | Today (${status.calendar_today || calendarToday}): ${status.attendance_logs_today || 0}`;
                if (status.last_punch_at) meta += ` | Last punch: ${status.last_punch_at}`;
                document.getElementById('status-meta').textContent = meta;
                document.getElementById('status-banner').classList.toggle('warn', !!status.needs_reexport);
            }

            document.getElementById('stat-total').textContent = summary.length;
            document.getElementById('stat-in').textContent = summary.filter(s => s.check_in).length;
            document.getElementById('stat-out').textContent = summary.filter(s => s.check_out).length;

            const summaryBody = document.getElementById('summary-body');
            if (summary.length === 0) {
                summaryBody.innerHTML = '<tr><td colspan="4" class="empty">No punches for this date. Click <strong>Show All</strong> to view all exported records.</td></tr>';
            } else {
                summaryBody.innerHTML = summary.map(row => `
                    <tr>
                        <td><strong>${row.device_user_id}</strong></td>
                        <td>${row.employee_name}</td>
                        <td>${row.check_in ? `<span class="badge badge-in">${row.check_in}</span>` : '<span class="badge badge-unknown">—</span>'}</td>
                        <td>${row.check_out ? `<span class="badge badge-out">${row.check_out}</span>` : '<span class="badge badge-unknown">—</span>'}</td>
                    </tr>
                `).join('');
            }

            const punches = data.punches || [];
            const punchesBody = document.getElementById('punches-body');
            if (punches.length === 0) {
                punchesBody.innerHTML = '<tr><td colspan="4" class="empty">No punch logs. Click <strong>Show All</strong> or change the date.</td></tr>';
            } else {
                punchesBody.innerHTML = punches.map(p => `
                    <tr>
                        <td><strong>${p.device_user_id}</strong></td>
                        <td>${p.employee_name}</td>
                        <td>${p.punch_time}</td>
                        <td><span class="badge ${badgeClass(p.punch_type)}">${badgeLabel(p.punch_type)}</span></td>
                    </tr>
                `).join('');
            }
        }

        function loadData() {
            const date = document.getElementById('date-filter').value;
            fetch(apiUrl + '?date=' + encodeURIComponent(date))
                .then(r => r.json())
                .then(renderData)
                .catch(() => {});
        }

        loadData();
        setInterval(loadData, refreshSeconds * 1000);
    </script>
</body>
</html>
