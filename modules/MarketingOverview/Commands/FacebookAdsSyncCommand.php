<?php

namespace Modules\MarketingOverview\Commands;

use Modules\MarketingOverview\Services\Sync\FacebookAdsSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Exception;
use Illuminate\Console\Command;

class FacebookAdsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:facebook:sync {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Facebook ads sync';

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
            $service->processService(FacebookAdsSyncService::class);
        } while($this->option('infinity'));
        return 0;
    }
}
