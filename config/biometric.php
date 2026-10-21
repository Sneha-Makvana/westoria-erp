<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Realtime Biometrics / ZKTeco Device Settings
    |--------------------------------------------------------------------------
    |
    | Configure your T304-Mini (or compatible) device here.
    | Device must be on the same LAN as the server for direct pull sync.
    |
    */

    'device' => [
        'name'         => env('BIOMETRIC_DEVICE_NAME', 'T304-Mini'),
        'ip'           => env('BIOMETRIC_IP', '192.168.1.8'),
        'port'         => (int) env('BIOMETRIC_PORT', 4370),
        'alt_port'     => (int) env('BIOMETRIC_ALT_PORT', 5005),
        'serial'       => env('BIOMETRIC_SERIAL', 'RSS202512133640'),
        'password'     => env('BIOMETRIC_PASSWORD', '0'),
        'connection'   => env('BIOMETRIC_CONNECTION', 'LAN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Push Server (ADMS Protocol)
    |--------------------------------------------------------------------------
    |
    | Point your device "Server IP" to this Laravel server and set the push URL
    | to: {APP_URL}/iclock/cdata
    |
    */

    'push' => [
        'enabled' => env('BIOMETRIC_PUSH_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    */

    'sync' => [
        'enabled'      => env('BIOMETRIC_SYNC_ENABLED', true),
        'days_back'    => (int) env('BIOMETRIC_SYNC_DAYS_BACK', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Display Page
    |--------------------------------------------------------------------------
    */

    'public' => [
        'auto_refresh_seconds' => (int) env('BIOMETRIC_PUBLIC_REFRESH', 30),
        'records_per_page'     => (int) env('BIOMETRIC_PUBLIC_LIMIT', 100),
        'timezone'             => env('BIOMETRIC_TIMEZONE', 'Asia/Kolkata'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Realtime Attendance Tracker — Parallel Database Export
    |--------------------------------------------------------------------------
    |
    | Configure Realtime desktop software (Attendance Tracker) to export into
    | this MySQL table. Use "Test Connection" in Realtime, then Save.
    |
    | Database Type: MySQL
    | Table Name: AttendanceLogs
    |
    */

    'realtime_export' => [
        'enabled'    => env('BIOMETRIC_REALTIME_EXPORT_ENABLED', true),
        'table'      => env('BIOMETRIC_REALTIME_TABLE', 'AttendanceLogs'),
        'auto_import'=> env('BIOMETRIC_REALTIME_AUTO_IMPORT', true),
    ],

    'test_key' => env('BIOMETRIC_TEST_KEY'),

];
