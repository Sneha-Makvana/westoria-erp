<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\BiometricPunch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BiometricDeviceService
{
    public function todayDate(): string
    {
        return Carbon::now($this->timezone())->toDateString();
    }

    public function timezone(): string
    {
        return config('biometric.public.timezone', 'Asia/Kolkata');
    }

    public function deviceConfig(): array
    {
        return config('biometric.device', []);
    }

    /**
     * Handle ADMS / ZKTeco push protocol attendance data.
     * Format per line: PIN\tTime\tStatus\tVerify\tWorkCode\tReserved
     */
    public function storePushPayload(string $rawBody, ?string $serial = null, ?string $deviceIp = null): int
    {
        $serial = $serial ?: ($this->deviceConfig()['serial'] ?? null);
        $stored = 0;

        foreach (preg_split('/\r\n|\r|\n/', trim($rawBody)) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parsed = $this->parseAttendanceLine($line);
            if (! $parsed) {
                continue;
            }

            if ($this->storePunch(
                deviceUserId: $parsed['device_user_id'],
                punchTime: $parsed['punch_time'],
                deviceState: $parsed['device_state'],
                verifyMode: $parsed['verify_mode'],
                deviceSerial: $serial,
                deviceIp: $deviceIp,
                source: 'push',
                rawPayload: $line
            )) {
                $stored++;
            }
        }

        return $stored;
    }

    public function parseAttendanceLine(string $line): ?array
    {
        if (str_contains($line, "\t")) {
            $parts = explode("\t", $line);
            $deviceUserId = trim($parts[0] ?? '');
            $timeStr = trim($parts[1] ?? '');
            $deviceState = isset($parts[2]) ? (int) trim($parts[2]) : null;
            $verifyMode = isset($parts[3]) ? (int) trim($parts[3]) : null;

            if ($deviceUserId === '' || $timeStr === '') {
                return null;
            }

            try {
                $punchTime = Carbon::parse($timeStr);
            } catch (\Throwable) {
                return null;
            }

            return [
                'device_user_id' => $deviceUserId,
                'punch_time'     => $punchTime,
                'device_state'   => $deviceState,
                'verify_mode'    => $verifyMode,
            ];
        }

        return null;
    }

    /**
     * Pull attendance logs directly from device using ZK protocol.
     */
    public function syncFromDevice(): array
    {
        if (! config('biometric.sync.enabled', true)) {
            return ['success' => false, 'message' => 'Device sync is disabled in config.', 'stored' => 0];
        }

        if (! class_exists(\Jmrashed\Zkteco\Lib\ZKTeco::class)) {
            return [
                'success' => false,
                'message' => 'ZK library not installed. Run: composer require jmrashed/zkteco',
                'stored'  => 0,
            ];
        }

        $device = $this->deviceConfig();
        $ip = $device['ip'] ?? null;
        $ports = array_values(array_unique(array_filter([
            (int) ($device['port'] ?? 4370),
            (int) ($device['alt_port'] ?? 5005),
        ])));

        $lastError = 'Could not connect to device.';
        $stored = 0;

        foreach ($ports as $port) {
            try {
                $zk = new \Jmrashed\Zkteco\Lib\ZKTeco($ip, $port);

                if (! $zk->connect()) {
                    $lastError = "Connection failed on {$ip}:{$port}";
                    continue;
                }

                $zk->disableDevice();
                $logs = $zk->getAttendance() ?: [];
                $zk->enableDevice();
                $zk->disconnect();

                $cutoff = now()->subDays((int) config('biometric.sync.days_back', 30));

                foreach ($logs as $log) {
                    $deviceUserId = (string) ($log['id'] ?? $log['uid'] ?? '');
                    $timestamp = $log['timestamp'] ?? null;

                    if ($deviceUserId === '' || ! $timestamp) {
                        continue;
                    }

                    try {
                        $punchTime = Carbon::parse($timestamp);
                    } catch (\Throwable) {
                        continue;
                    }

                    if ($punchTime->lt($cutoff)) {
                        continue;
                    }

                    if ($this->storePunch(
                        deviceUserId: $deviceUserId,
                        punchTime: $punchTime,
                        deviceState: isset($log['state']) ? (int) $log['state'] : null,
                        verifyMode: isset($log['type']) ? (int) $log['type'] : null,
                        deviceSerial: $device['serial'] ?? null,
                        deviceIp: $ip,
                        source: 'pull',
                        rawPayload: json_encode($log)
                    )) {
                        $stored++;
                    }
                }

                return [
                    'success' => true,
                    'message' => "Synced {$stored} punch(es) from {$ip}:{$port}",
                    'stored'  => $stored,
                    'port'    => $port,
                ];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('Biometric device sync failed', ['ip' => $ip, 'port' => $port, 'error' => $e->getMessage()]);
            }
        }

        return ['success' => false, 'message' => $lastError, 'stored' => $stored];
    }

    /**
     * Import punches from Realtime Attendance Tracker parallel DB export table.
     */
    public function importFromRealtimeExport(): array
    {
        if (! config('biometric.realtime_export.enabled', true)) {
            return ['success' => false, 'message' => 'Realtime export import is disabled.', 'stored' => 0];
        }

        if (! $this->realtimeTableExists()) {
            return [
                'success' => false,
                'message' => 'AttendanceLogs table not found. Run: php artisan migrate',
                'stored'  => 0,
            ];
        }

        $stored = 0;
        $table = config('biometric.realtime_export.table', 'AttendanceLogs');

        $select = [
            'EmployeeID',
            'EmployeeCode',
            'LogDateTime',
            'Direction',
            'DeviceSerialNumber',
            'DeviceipAddress',
        ];

        if (Schema::hasTable('biometric_realtime_imported_keys')) {
            $rows = DB::table($table . ' as a')
                ->leftJoin('biometric_realtime_imported_keys as i', function ($join) {
                    $join->on('a.EmployeeID', '=', 'i.employee_id')
                        ->on('a.LogDateTime', '=', 'i.log_datetime')
                        ->on('a.Direction', '=', 'i.direction');
                })
                ->whereNull('i.id')
                ->orderBy('a.LogDateTime')
                ->limit(500)
                ->get(array_map(fn ($c) => 'a.' . $c, $select));
        } else {
            $rows = DB::table($table)
                ->orderBy('LogDateTime')
                ->limit(500)
                ->get($select);
        }

        foreach ($rows as $row) {
            $deviceUserId = $this->realtimeDeviceUserId($row);
            $punchTime = $this->realtimePunchTime($row);

            if ($deviceUserId === '' || ! $punchTime) {
                $this->markRealtimeRowImported($row);
                continue;
            }

            $direction = strtolower(trim((string) ($row->Direction ?? '')));
            $deviceState = match (true) {
                in_array($direction, ['in', 'check in', 'check-in', 'check_in'], true) => 0,
                in_array($direction, ['out', 'check out', 'check-out', 'check_out'], true) => 1,
                default => null,
            };

            if ($this->storePunch(
                deviceUserId: $deviceUserId,
                punchTime: $punchTime,
                deviceState: $deviceState,
                verifyMode: null,
                deviceSerial: $row->DeviceSerialNumber ?? null,
                deviceIp: $row->DeviceipAddress ?? null,
                source: 'realtime_export',
                rawPayload: json_encode([
                    'EmployeeID'   => $row->EmployeeID ?? null,
                    'EmployeeCode' => $row->EmployeeCode ?? null,
                    'Direction'    => $row->Direction ?? null,
                ])
            )) {
                $stored++;
            }

            $this->markRealtimeRowImported($row);
        }

        return [
            'success' => true,
            'message' => "Imported {$stored} punch(es) from Realtime AttendanceLogs",
            'stored'  => $stored,
        ];
    }

    public function realtimeTableExists(): bool
    {
        $table = config('biometric.realtime_export.table', 'AttendanceLogs');

        return Schema::hasTable($table);
    }

    protected function realtimeDeviceUserId(object $row): string
    {
        $code = trim((string) ($row->EmployeeCode ?? ''));
        if ($code !== '' && $code !== '0') {
            return $code;
        }

        if (! empty($row->EmployeeID)) {
            return (string) $row->EmployeeID;
        }

        return '';
    }

    protected function realtimePunchTime(object $row): ?Carbon
    {
        if (! empty($row->LogDateTime)) {
            try {
                $dt = Carbon::parse($row->LogDateTime);
                if ($dt->year > 1971) {
                    return $dt;
                }
            } catch (\Throwable) {
                //
            }
        }

        if (! empty($row->LogDate)) {
            try {
                $date = Carbon::parse($row->LogDate);
                if ($date->year > 1971) {
                    $time = ! empty($row->LogTime) ? trim((string) $row->LogTime) : '00:00:00';

                    return Carbon::parse($date->toDateString() . ' ' . $time);
                }
            } catch (\Throwable) {
                //
            }
        }

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected function fetchRealtimeLogRows(?string $date = null, ?int $limit = null)
    {
        $table = config('biometric.realtime_export.table', 'AttendanceLogs');
        $limit = $limit ?? 500;

        return DB::table($table)
            ->orderByDesc('LogDateTime')
            ->limit($limit * 3)
            ->get()
            ->map(function ($row) {
                $row->_parsed_time = $this->realtimePunchTime($row);

                return $row;
            })
            ->filter(fn ($row) => $row->_parsed_time !== null)
            ->when($date && $date !== 'all', function ($collection) use ($date) {
                return $collection->filter(fn ($row) => $row->_parsed_time->toDateString() === $date);
            })
            ->take($limit)
            ->values();
    }

    public function getLatestAttendanceLogDate(): ?string
    {
        $latest = $this->fetchRealtimeLogRows('all', 1)->first();

        return $latest?->_parsed_time?->toDateString();
    }

    public function getLastPunchDateTime(): ?string
    {
        $latest = $this->fetchRealtimeLogRows('all', 1)->first();

        return $latest?->_parsed_time?->format('Y-m-d H:i:s');
    }

    public function getAttendanceLogDateRange(): array
    {
        $rows = $this->fetchRealtimeLogRows('all', 1000);
        if ($rows->isEmpty()) {
            return ['min' => null, 'max' => null, 'dates' => []];
        }

        $dates = $rows->map(fn ($r) => $r->_parsed_time->toDateString())->unique()->sort()->values()->all();

        return [
            'min'   => $dates[0] ?? null,
            'max'   => $dates[array_key_last($dates)] ?? null,
            'dates' => $dates,
        ];
    }

    protected function markRealtimeRowImported(object $row): void
    {
        if (! Schema::hasTable('biometric_realtime_imported_keys')) {
            return;
        }

        try {
            DB::table('biometric_realtime_imported_keys')->updateOrInsert(
                [
                    'employee_id'  => (int) ($row->EmployeeID ?? 0),
                    'log_datetime' => $row->LogDateTime,
                    'direction'    => (string) ($row->Direction ?? ''),
                ],
                ['imported_at' => now()]
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to mark realtime row imported', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Import Realtime export rows then return daily summary.
     */
    public function refreshAndGetDailySummary(?string $date = null): array
    {
        if (config('biometric.realtime_export.auto_import', true)) {
            $this->importFromRealtimeExport();
        }

        return $this->getPublicSummary($date);
    }

    public function getIntegrationStatus(?string $date = null): array
    {
        $calendarToday = $this->todayDate();
        $date = $date ?: $calendarToday;
        $tableExists = $this->realtimeTableExists();

        $attendanceLogsCount = 0;
        $forSelectedDate = 0;
        $forCalendarToday = 0;

        if ($tableExists) {
            $parsedRows = $this->fetchRealtimeLogRows('all', 2000);
            $attendanceLogsCount = $parsedRows->count();
            $forSelectedDate = $parsedRows->filter(fn ($r) => $r->_parsed_time->toDateString() === $date)->count();
            $forCalendarToday = $parsedRows->filter(fn ($r) => $r->_parsed_time->toDateString() === $calendarToday)->count();
        }

        $punchesToday = (int) BiometricPunch::where('punch_date', $date)->count();
        $dateRange = $this->getAttendanceLogDateRange();
        $lastPunch = $this->getLastPunchDateTime();

        $needsReExport = $forCalendarToday === 0 && $attendanceLogsCount > 0
            && $lastPunch && Carbon::parse($lastPunch)->toDateString() < $calendarToday;

        $message = match (true) {
            ! $tableExists => 'AttendanceLogs table missing. Run php artisan migrate on server.',
            $attendanceLogsCount === 0 => 'No punch data yet. In Realtime: Data Transfer from device, then Manual Export.',
            $needsReExport => "Today's punches ({$calendarToday}) not in database yet. In Realtime: 1) Data Transfer 2) set To Date = today 3) Manual Export again.",
            $forCalendarToday > 0 && $date === $calendarToday => "Showing {$forCalendarToday} punch(es) for today ({$calendarToday}).",
            $forSelectedDate === 0 && $attendanceLogsCount > 0 => "No punches on {$date}. Today ({$calendarToday}): {$forCalendarToday}. Last punch in DB: {$lastPunch}.",
            $forSelectedDate > 0 => "Showing {$forSelectedDate} punch(es) for {$date}.",
            default => 'Connected.',
        };

        return [
            'table_exists'             => $tableExists,
            'attendance_logs_total'    => $attendanceLogsCount,
            'attendance_logs_today'    => $forCalendarToday,
            'attendance_logs_selected' => $forSelectedDate,
            'calendar_today'           => $calendarToday,
            'selected_date'            => $date,
            'biometric_punches_today'  => $punchesToday,
            'latest_date'              => $dateRange['max'],
            'last_punch_at'            => $lastPunch,
            'needs_reexport'           => $needsReExport,
            'date_range'               => $dateRange,
            'message'                  => $message,
        ];
    }

    public function getPublicPunches(?string $date = null, ?int $limit = null): array
    {
        $date = $date ?: $this->todayDate();
        $limit = $limit ?? (int) config('biometric.public.records_per_page', 100);
        $punches = [];

        if ($this->realtimeTableExists()) {
            $rows = $this->fetchRealtimeLogRows($date, $limit);

            foreach ($rows as $row) {
                $punchTime = $row->_parsed_time;
                $deviceUserId = $this->realtimeDeviceUserId($row);

                if ($deviceUserId === '' || ! $punchTime) {
                    continue;
                }

                $direction = strtolower(trim((string) ($row->Direction ?? '')));
                $deviceState = match (true) {
                    in_array($direction, ['in', 'check in', 'check-in', 'check_in'], true) => 0,
                    in_array($direction, ['out', 'check out', 'check-out', 'check_out'], true) => 1,
                    default => null,
                };

                $punchType = $this->resolvePunchType($deviceUserId, $punchTime, $deviceState);
                $user = $this->resolveUser($deviceUserId);

                $punches[] = [
                    'id'             => 'rt-' . md5($deviceUserId . $punchTime->format('Y-m-d H:i:s') . $direction),
                    'device_user_id' => $deviceUserId,
                    'user_id'        => $user?->id,
                    'employee_name'  => $user?->name ?? ('Employee #' . $deviceUserId),
                    'punch_time'     => $punchTime->format('Y-m-d H:i:s'),
                    'punch_type'     => $punchType,
                    'verify_mode'    => null,
                    'source'         => 'realtime_export',
                    'direction'      => $row->Direction ?? '',
                ];
            }
        }

        if ($date !== 'all') {
            $erpPunches = BiometricPunch::with('user')
                ->where('punch_date', $date)
                ->orderByDesc('punch_time')
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'id'             => 'bp-' . $p->id,
                    'device_user_id' => $p->device_user_id,
                    'user_id'        => $p->user_id,
                    'employee_name'  => $p->employee_name ?: ($p->user?->name ?? 'Unknown'),
                    'punch_time'     => $p->punch_time->format('Y-m-d H:i:s'),
                    'punch_type'     => $p->punch_type,
                    'verify_mode'    => $p->verify_mode,
                    'source'         => $p->source,
                    'direction'      => $p->punch_type === 'check_in' ? 'In' : ($p->punch_type === 'check_out' ? 'Out' : ''),
                ])
                ->all();

            $punches = array_merge($punches, $erpPunches);
        }

        return collect($punches)
            ->unique(fn ($p) => $p['device_user_id'] . '|' . $p['punch_time'] . '|' . $p['punch_type'])
            ->sortByDesc('punch_time')
            ->values()
            ->take($limit)
            ->all();
    }

    public function getPublicSummary(?string $date = null): array
    {
        if ($date === 'all') {
            return $this->buildSummaryFromPunchList($this->getPublicPunches('all', 500));
        }

        $date = $date ?: $this->todayDate();

        return $this->buildSummaryFromPunchList($this->getPublicPunches($date, 500));
    }

    protected function buildSummaryFromPunchList(array $punches): array
    {
        $grouped = [];

        foreach ($punches as $punch) {
            $key = $punch['device_user_id'];
            $time = Carbon::parse($punch['punch_time'])->format('H:i:s');

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'device_user_id' => $punch['device_user_id'],
                    'user_id'        => $punch['user_id'],
                    'employee_name'  => $punch['employee_name'],
                    'check_in'       => null,
                    'check_out'      => null,
                    'punches'        => [],
                ];
            }

            $grouped[$key]['punches'][] = ['time' => $time, 'type' => $punch['punch_type']];

            if ($punch['punch_type'] === 'check_in' && ! $grouped[$key]['check_in']) {
                $grouped[$key]['check_in'] = $time;
            }

            if ($punch['punch_type'] === 'check_out') {
                $grouped[$key]['check_out'] = $time;
            }
        }

        return array_values($grouped);
    }

    public function storePunch(
        string $deviceUserId,
        Carbon $punchTime,
        ?int $deviceState = null,
        ?int $verifyMode = null,
        ?string $deviceSerial = null,
        ?string $deviceIp = null,
        string $source = 'push',
        ?string $rawPayload = null
    ): bool {
        $user = $this->resolveUser($deviceUserId);
        $punchType = $this->resolvePunchType($deviceUserId, $punchTime, $deviceState);

        try {
            BiometricPunch::updateOrCreate(
                [
                    'device_serial'  => $deviceSerial,
                    'device_user_id' => $deviceUserId,
                    'punch_time'     => $punchTime->format('Y-m-d H:i:s'),
                    'device_state'   => $deviceState,
                ],
                [
                    'user_id'        => $user?->id,
                    'employee_name'  => $user?->name,
                    'punch_date'     => $punchTime->toDateString(),
                    'punch_type'     => $punchType,
                    'verify_mode'    => $verifyMode,
                    'device_ip'      => $deviceIp,
                    'source'         => $source,
                    'raw_payload'    => $rawPayload ? mb_substr($rawPayload, 0, 500) : null,
                ]
            );

            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to store biometric punch', [
                'device_user_id' => $deviceUserId,
                'punch_time'     => $punchTime->toDateTimeString(),
                'error'          => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function resolveUser(string $deviceUserId): ?object
    {
        $mapped = DB::table('user_details')
            ->join('users', 'users.id', '=', 'user_details.user_id')
            ->where('user_details.biometric_enroll_id', $deviceUserId)
            ->where('users.isDeleted', '!=', 1)
            ->select('users.id', 'users.name')
            ->first();

        if ($mapped) {
            return $mapped;
        }

        if (ctype_digit($deviceUserId)) {
            return DB::table('users')
                ->where('id', (int) $deviceUserId)
                ->where('isDeleted', '!=', 1)
                ->select('id', 'name')
                ->first();
        }

        return null;
    }

    public function resolvePunchType(string $deviceUserId, Carbon $punchTime, ?int $deviceState): string
    {
        if ($deviceState === 0) {
            return 'check_in';
        }

        if ($deviceState === 1) {
            return 'check_out';
        }

        $date = $punchTime->toDateString();
        $previous = BiometricPunch::where('device_user_id', $deviceUserId)
            ->where('punch_date', $date)
            ->where('punch_time', '<', $punchTime->format('Y-m-d H:i:s'))
            ->orderByDesc('punch_time')
            ->value('punch_type');

        if ($previous === 'check_in') {
            return 'check_out';
        }

        if ($previous === 'check_out') {
            return 'check_in';
        }

        return 'check_in';
    }

    public function getDailySummary(?string $date = null): array
    {
        $date = $date ?: now()->toDateString();

        $punches = BiometricPunch::with('user')
            ->where('punch_date', $date)
            ->orderBy('punch_time')
            ->get();

        $grouped = [];

        foreach ($punches as $punch) {
            $key = $punch->device_user_id;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'device_user_id' => $punch->device_user_id,
                    'user_id'        => $punch->user_id,
                    'employee_name'  => $punch->employee_name ?: ($punch->user?->name ?? 'Unknown'),
                    'check_in'       => null,
                    'check_out'      => null,
                    'punches'        => [],
                ];
            }

            $grouped[$key]['punches'][] = [
                'time' => $punch->punch_time->format('H:i:s'),
                'type' => $punch->punch_type,
            ];

            if ($punch->punch_type === 'check_in' && ! $grouped[$key]['check_in']) {
                $grouped[$key]['check_in'] = $punch->punch_time->format('H:i:s');
            }

            if ($punch->punch_type === 'check_out') {
                $grouped[$key]['check_out'] = $punch->punch_time->format('H:i:s');
            }
        }

        return array_values($grouped);
    }

    /**
     * Insert punch rows into AttendanceLogs (used by JSON import / manual API).
     */
    public function insertAttendanceLogRow(array $data): bool
    {
        if (! $this->realtimeTableExists()) {
            return false;
        }

        $table = config('biometric.realtime_export.table', 'AttendanceLogs');
        $columns = Schema::getColumnListing($table);
        $punchTime = Carbon::parse($data['punch_time'] ?? now());

        $row = array_filter([
            'EmployeeID'         => (int) ($data['employee_id'] ?? $data['EmployeeID'] ?? 0),
            'EmployeeCode'       => (string) ($data['employee_code'] ?? $data['EmployeeCode'] ?? $data['employee_id'] ?? ''),
            'LogDateTime'        => $punchTime->format('Y-m-d H:i:s'),
            'LogDate'            => $punchTime->toDateString(),
            'LogTime'            => $punchTime->format('H:i:s'),
            'Direction'          => (string) ($data['direction'] ?? $data['Direction'] ?? 'In'),
            'DeviceSerialNumber' => (string) ($data['device_serial'] ?? config('biometric.device.serial', '')),
            'DeviceipAddress'    => (string) ($data['device_ip'] ?? config('biometric.device.ip', '')),
        ], fn ($v, $k) => in_array($k, $columns, true), ARRAY_FILTER_USE_BOTH);

        if (empty($row)) {
            return false;
        }

        try {
            DB::table($table)->insert($row);

            return true;
        } catch (\Throwable $e) {
            Log::warning('AttendanceLogs insert failed', ['error' => $e->getMessage(), 'row' => $row]);

            return false;
        }
    }

    public function importPunchesJson(array $punches): array
    {
        $stored = 0;
        $failed = 0;

        foreach ($punches as $punch) {
            if (! is_array($punch)) {
                $failed++;
                continue;
            }

            if ($this->insertAttendanceLogRow($punch)) {
                $stored++;
            } else {
                $failed++;
            }
        }

        if ($stored > 0) {
            $this->importFromRealtimeExport();
        }

        return [
            'success' => $stored > 0,
            'message' => "Imported {$stored} punch(es)" . ($failed > 0 ? ", {$failed} failed" : ''),
            'stored'  => $stored,
            'failed'  => $failed,
        ];
    }
}
