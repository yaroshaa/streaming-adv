<?php

namespace Modules\MarketingOverview\Commands;

use Modules\MarketingOverview\Services\Sync\GoogleAdsMarketingSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Exception;
use Illuminate\Console\Command;

/**
 * Class GoogleAdsSyncCommand
 * @package App\Console\Commands\Marketing
 */
class GoogleAdsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:google-ads:sync {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cost of google ads';

    /**
     * Execute the console command.
     *
     * @param MarketingOverviewSyncService $service
     * @return int
     * @throws Exception
     */
    public function handle(MarketingOverviewSyncService $service): int
    {
        do {
            $service->processService(GoogleAdsMarketingSyncService::class);
        } while($this->option('infinity'));

        return 0;
    }
}
