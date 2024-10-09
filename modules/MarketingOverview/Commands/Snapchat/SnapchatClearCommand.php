<?php

namespace Modules\MarketingOverview\Commands\Snapchat;

use Modules\MarketingOverview\Services\Sync\MarketingOverviewSyncService;
use Modules\MarketingOverview\Services\Sync\SnapchatAdsMarketingSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SnapchatClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing-overview:snapchat:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing cache for snapchat ads integration';

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
            $this->info('Clearing cache for: ' . $account['client_id']);
            Cache::forget('snapchat.' . $account['client_id'] . '.token');
            Cache::forget('snapchat.' . $account['client_id'] . '.refresh_token');
        }
        return 0;
    }
}
