<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductImagesToBranchesCommand extends Command
{
    protected $signature = 'products:sync-images';

    protected $description = 'Copy product images to the same product in other branches where that copy has no image';

    public function handle(): int
    {
        @set_time_limit(0);

        $this->info('Copying product images to branch copies without images...');
        $filled = Product::backfillBranchCopyImages();
        $this->info("Done. {$filled} branch product(s) received images.");

        return self::SUCCESS;
    }
}
