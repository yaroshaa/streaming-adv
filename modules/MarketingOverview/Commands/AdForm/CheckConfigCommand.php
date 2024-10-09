<?php

namespace Modules\MarketingOverview\Commands\AdForm;

use Exception;
use Illuminate\Console\Command;
use Modules\MarketingOverview\Services\Sync\AdForm\RequestBag;
use Modules\MarketingOverview\Services\Sync\AdFormAdsMarketingSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;

class CheckConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:ad-form:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ad form - check config';

    /**
     * Execute the console command.
     *
     * @param MarketingOverviewSyncService $service
     * @return int
     */
    public function handle(MarketingOverviewSyncService $service): int
    {
        $adFormAccount = $service->getConfigBySyncService(AdFormAdsMarketingSyncService::class);
        foreach ($adFormAccount as $account) {
            try {
                $requestBag = new RequestBag($account);
                $this->info(json_encode($account));
                $this->info(sprintf('Amount: %f', $requestBag->getAmount()));
            } catch (Exception $exception) {
                $this->warn($exception);
            }
        }
        $this->info(sprintf('See more in "%s"', base_path('storage/logs/laravel.log')));
        return 0;
    }
}
