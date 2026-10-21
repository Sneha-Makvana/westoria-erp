<?php

namespace App\Http\Controllers;

use App\Services\BiometricDeviceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BiometricPunchController extends Controller
{
    public function __construct(
        protected BiometricDeviceService $biometric
    ) {}

    public function publicPage(Request $request)
    {
        $date = $request->query('date', $this->biometric->todayDate());
        $device = $this->biometric->deviceConfig();

        return view('biometric.punches', [
            'date'           => $date,
            'calendarToday'  => $this->biometric->todayDate(),
            'device'         => $device,
            'summary'        => $this->biometric->getPublicSummary($date),
            'status'         => $this->biometric->getIntegrationStatus($date),
            'refreshSeconds' => config('biometric.public.auto_refresh_seconds', 30),
        ]);
    }

    public function publicData(Request $request)
    {
        $date = $request->query('date', $this->biometric->todayDate());
        $limit = (int) config('biometric.public.records_per_page', 100);

        if (config('biometric.realtime_export.auto_import', true)) {
            $this->biometric->importFromRealtimeExport();
        }

        $punches = $this->biometric->getPublicPunches($date, $limit);
        $summary = $date === 'all'
            ? $this->buildSummaryFromPunches($punches)
            : $this->biometric->getPublicSummary($date);

        $statusDate = ($date === 'all') ? $this->biometric->todayDate() : $date;

        return response()->json([
            'date'    => $date,
            'summary' => $summary,
            'punches' => $punches,
            'device'  => $this->biometric->deviceConfig(),
            'status'  => $this->biometric->getIntegrationStatus($statusDate),
        ]);
    }

    protected function buildSummaryFromPunches(array $punches): array
    {
        $grouped = [];

        foreach ($punches as $punch) {
            $key = $punch['device_user_id'];
            $time = \Carbon\Carbon::parse($punch['punch_time'])->format('H:i:s');

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

    public function insertTestRow(Request $request)
    {
        $key = config('biometric.test_key');
        if (! $key || $request->query('key') !== $key) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (! $this->biometric->realtimeTableExists()) {
            return response()->json(['success' => false, 'message' => 'AttendanceLogs table not found'], 422);
        }

        $now = now();
        $inserted = $this->biometric->insertAttendanceLogRow([
            'employee_id'   => 1,
            'employee_code' => '1',
            'punch_time'    => $now->toDateTimeString(),
            'direction'     => 'In',
        ]);

        if (! $inserted) {
            return response()->json(['success' => false, 'message' => 'Insert failed — check AttendanceLogs table columns'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test punch inserted. Refresh the public page.',
            'time'    => $now->toDateTimeString(),
        ]);
    }

    public function importJson(Request $request)
    {
        $key = config('biometric.test_key');
        if (! $key || $request->query('key') !== $key) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $punches = $request->input('punches', $request->json('punches', []));

        if (! is_array($punches) || empty($punches)) {
            return response()->json([
                'success' => false,
                'message' => 'Send JSON: {"punches":[{"employee_id":"1","punch_time":"2026-06-17 09:00:00","direction":"In"}]}',
            ], 422);
        }

        $result = $this->biometric->importPunchesJson($punches);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function importRealtimeExport()
    {
        $result = $this->biometric->importFromRealtimeExport();

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function receivePush(Request $request): Response
    {
        if (! config('biometric.push.enabled', true)) {
            return response('OK', 200);
        }

        $serial = $request->query('SN') ?: $request->input('SN');
        $table = $request->query('table') ?: $request->input('table');
        $rawBody = $request->getContent();

        if ($table && ! in_array($table, ['ATTLOG', 'OPERLOG', 'ATTPHOTO'], true)) {
            return response('OK', 200);
        }

        if ($rawBody !== '') {
            $this->biometric->storePushPayload($rawBody, $serial, $request->ip());
        }

        return response('OK', 200);
    }

    public function heartbeat(Request $request): Response
    {
        return response('OK', 200);
    }

    public function syncFromDevice()
    {
        $result = $this->biometric->syncFromDevice();

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
