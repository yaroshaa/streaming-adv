<?php

namespace Modules\MarketingOverview\Commands\Snapchat;

use Illuminate\Support\Facades\Cache;
use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Modules\MarketingOverview\Services\Sync\SnapchatAdsMarketingSyncService;
use Illuminate\Console\Command;

class SnapchatCredentialsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:snapchat:credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show client id and current token';

    /**
     * Execute the console command.
     *
     * @param MarketingOverviewSyncService $service
     * @return int
     */
    public function handle(MarketingOverviewSyncService $service): int
    {
        $snapchatAccounts = $service->getConfigBySyncService(SnapchatAdsMarketingSyncService::class);
        foreach ($snapchatAccounts as $account) {
            $this->info('Client-id: ' . $account['client_id']);
            $this->info('Token: ' . Cache::get('snapchat.' . $account['client_id'] . '.token'));
            $this->info('Refresh token: ' . Cache::get('snapchat.' . $account['client_id'] . '.refresh_token'));

        }

        return 0;
    }
}
