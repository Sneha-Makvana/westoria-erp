<?php

namespace App\Console\Commands;

use App\Services\BiometricDeviceService;
use Illuminate\Console\Command;

class SyncBiometricPunches extends Command
{
    protected $signature = 'biometric:sync {--realtime : Import from Realtime AttendanceLogs table only}';

    protected $description = 'Sync biometric punches from device and/or Realtime database export';

    public function handle(BiometricDeviceService $biometric): int
    {
        if ($this->option('realtime')) {
            $this->info('Importing from Realtime AttendanceLogs...');
            $result = $biometric->importFromRealtimeExport();
        } else {
            $this->info('Syncing biometric punches from device...');
            $result = $biometric->syncFromDevice();

            if (config('biometric.realtime_export.enabled', true)) {
                $import = $biometric->importFromRealtimeExport();
                if ($import['stored'] > 0) {
                    $this->info($import['message']);
                }
            }
        }

        if ($result['success']) {
            $this->info($result['message']);

            return self::SUCCESS;
        }

        $this->error($result['message']);

        return self::FAILURE;
    }
}
