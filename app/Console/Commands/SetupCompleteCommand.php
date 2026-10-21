<?php

namespace App\Console\Commands;

use App\Services\Setup\InstallerService;
use Illuminate\Console\Command;

class SetupCompleteCommand extends Command
{
    protected $signature = 'setup:complete';

    protected $description = 'Mark the application as installed (after manual migrate/setup)';

    public function handle(InstallerService $installer): int
    {
        if ($installer->isInstalled()) {
            $this->info('Application is already marked as installed.');

            return self::SUCCESS;
        }

        $installer->markInstalled();
        $this->info('Install lock file created. Web setup wizard is now disabled.');

        return self::SUCCESS;
    }
}
