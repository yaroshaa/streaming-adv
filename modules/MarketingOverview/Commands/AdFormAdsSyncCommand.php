<?php

namespace Modules\MarketingOverview\Commands;

use Modules\MarketingOverview\Services\Sync\AdFormAdsMarketingSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Exception;
use Illuminate\Console\Command;

/**
 * Class AdFormAdsSyncCommand
 * @package App\Console\Commands\Marketing
 */
class AdFormAdsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:ad-form:sync {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cost of adform';

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
            $service->processService(AdFormAdsMarketingSyncService::class);
        } while($this->option('infinity'));

        return 0;
    }
}
