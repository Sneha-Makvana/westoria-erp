<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\ClientRepository;

class ResetPassportClients extends Command
{
    protected $signature = 'passport:reset-clients {--force : Skip confirmation}';

    protected $description = 'Clear OAuth tables and recreate Passport clients (fixes "Client authentication failed" on login)';

    public function handle(ClientRepository $clients): int
    {
        if (! $this->option('force') && ! $this->confirm('This deletes all OAuth clients and tokens. Continue?', true)) {
            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();

        foreach ([
            'oauth_access_tokens',
            'oauth_refresh_tokens',
            'oauth_auth_codes',
            'oauth_personal_access_clients',
            'oauth_clients',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        $appName = config('app.name', 'Fablead ERP');

        $personalClient = $clients->createPersonalAccessClient(
            null,
            $appName . ' Personal Access Client',
            'http://localhost'
        );

        $clients->createPasswordGrantClient(
            null,
            $appName . ' Password Grant Client',
            'http://localhost',
            'users'
        );

        $this->newLine();
        $this->info('Passport clients reset successfully.');
        $this->line("Personal access client ID: {$personalClient->id}");
        $this->line('Log out, then log in again at the sign-in page.');

        return self::SUCCESS;
    }
}
