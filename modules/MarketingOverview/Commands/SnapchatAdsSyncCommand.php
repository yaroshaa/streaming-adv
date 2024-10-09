<?php

namespace Modules\MarketingOverview\Commands;

use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Modules\MarketingOverview\Services\Sync\SnapchatAdsMarketingSyncService;
use Exception;
use Illuminate\Console\Command;

class SnapchatAdsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:snapchat:sync {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync snapchat ads stats';

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
            $service->processService(SnapchatAdsMarketingSyncService::class);
        } while($this->option('infinity'));

        return 0;
    }
}
