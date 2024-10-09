<?php

namespace Modules\MarketingOverview\Services\Sync;

interface MarketingSyncServiceSkipCheckAmountInterface
{
    public function skipAmountCheckByInterval(): bool;
}
