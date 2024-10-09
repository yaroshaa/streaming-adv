<?php

namespace Modules\MarketingOverview\Commands\AdForm;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Modules\MarketingOverview\Services\Sync\AdForm\RequestBag;
use Modules\MarketingOverview\Services\Sync\AdFormAdsMarketingSyncService;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;

class CleanUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:ad-form:clean-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ad form - cleanup operations (reports)';

    /**
     * Execute the console command.
     *
     * @param MarketingOverviewSyncService $service
     * @return int
     * @throws RequestException
     */
    public function handle(MarketingOverviewSyncService $service): int
    {
        $adFormAccount = $service->getConfigBySyncService(AdFormAdsMarketingSyncService::class);
        foreach ($adFormAccount as $account) {
            $requestBag = new RequestBag($account);
            foreach ($requestBag->listOfOperations() as $operation) {
                $this->info(sprintf('Operation processing "%s"', $operation['location']));

                $requestBag->deleteOperation($operation['id']);

                $this->info('Operation deleted');
            }
        }
        return 0;
    }
}
