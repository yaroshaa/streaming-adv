<?php

namespace Modules\MarketingOverview\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\MarketingOverview\Services\Sync\PerformissionAdsMarketingSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;

/**
 * Class PerformissionAdsSyncCommand
 * @package App\Console\Commands\Marketing
 */
class PerformissionAdsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:performission:sync {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cost of performission.no';

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
            $service->processService(PerformissionAdsMarketingSyncService::class);
        } while($this->option('infinity'));

        return 0;
    }
}
