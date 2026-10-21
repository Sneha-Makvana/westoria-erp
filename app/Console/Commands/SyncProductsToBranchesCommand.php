<?php

namespace App\Console\Commands;

use App\Services\BranchCatalogSyncService;
use Illuminate\Console\Command;

class SyncProductsToBranchesCommand extends Command
{
    protected $signature = 'products:sync-to-branches
                            {--source= : Source branch user id (defaults to first admin)}
                            {--chunk=120 : Products per chunk}
                            {--reset : Clear saved cursor and re-check from the start}';

    protected $description = 'Copy ALL Main Branch products to every other branch (qty 0, stock stays local)';

    public function handle(BranchCatalogSyncService $sync): int
    {
        @set_time_limit(0);

        $source = (int) ($this->option('source') ?: 0) ?: null;
        $chunk = max(20, min(200, (int) $this->option('chunk')));
        $reset = (bool) $this->option('reset');

        $this->info('Copying ALL products to all branches...');
        if ($reset) {
            $this->warn('Reset mode: starting from the first missing product.');
        }

        $bar = null;
        $lastProcessed = 0;

        try {
            $result = $sync->run($source, $chunk, $reset, function (array $status) use (&$bar, &$lastProcessed) {
                $sourceCount = max(1, (int) ($status['source_count'] ?? 1));
                $processed = (int) ($status['processed'] ?? 0);
                $created = (int) ($status['created'] ?? 0);
                $remaining = (int) ($status['remaining'] ?? 0);

                if ($bar === null) {
                    $bar = $this->output->createProgressBar($sourceCount);
                    $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | created:%message%');
                    $bar->setMessage((string) $created);
                    $bar->start();
                }

                $advance = max(0, $processed - $lastProcessed);
                if ($advance > 0) {
                    $bar->advance($advance);
                    $lastProcessed = $processed;
                }
                $bar->setMessage((string) $created);

                // Also print a readable line every chunk for SSH logs.
                if ($processed > 0 && $processed % 500 < 20) {
                    $counts = $status['target_counts'] ?? [];
                    $countText = collect($counts)->map(fn ($c, $id) => "B{$id}={$c}")->implode(', ');
                    $this->output->writeln('');
                    $this->line("Processed {$processed} | Remaining ~{$remaining} | Created {$created} | {$countText}");
                }
            });
        } catch (\Throwable $e) {
            if ($bar) {
                $bar->finish();
                $this->newLine(2);
            }
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($bar) {
            $bar->finish();
            $this->newLine(2);
        } else {
            $this->newLine();
        }

        $this->info($result['message'] ?? 'Done');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Source products', $result['source_count'] ?? 0],
                ['Processed', $result['processed'] ?? 0],
                ['Created', $result['created'] ?? 0],
                ['Skipped', $result['skipped'] ?? 0],
                ['Failed', $result['failed'] ?? 0],
            ]
        );

        foreach (($result['target_counts'] ?? []) as $branchId => $count) {
            $sourceCount = (int) ($result['source_count'] ?? 0);
            $mark = ((int) $count >= $sourceCount) ? 'OK' : 'INCOMPLETE';
            $this->line("Branch {$branchId}: {$count} products [{$mark}]");
        }

        $done = ! empty($result['done']);

        return $done ? self::SUCCESS : self::FAILURE;
    }
}
