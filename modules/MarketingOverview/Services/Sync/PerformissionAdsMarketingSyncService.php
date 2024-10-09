<?php

namespace Modules\MarketingOverview\Services\Sync;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PerformissionAdsMarketingSyncService extends BaseSyncService implements MarketingSyncServiceInterface, MarketingSyncServiceSkipCheckAmountInterface
{
    public function init()
    {
        if (empty($this->config['api_token'])) {
            throw new Exception("Api token not exist");
        }

        if (empty($this->config['offer_sid'])) {
            throw new Exception("Offer sid not exist");
        }
    }
    public static function alias(): string
    {
        return 'performission';
    }

    public function skipAmountCheckByInterval(): bool
    {
        return true;
    }

    public static function getIntervalOnSeconds(): int
    {
        return 60 * 15;
    }

    public function getAmount(): float
    {
        $query = Arr::query([
            'offerSid' => $this->config['offer_sid'],
            'savedFrom' => $this->periodFrom()->format('Y-m-d'),
            'savedTo' => $this->periodFrom()->format('Y-m-d'),
            'status' => '1,3,5,6,7'// @todo, actual status?
        ]);
        $transactionsResponse = Http::withHeaders([
            'X-Api-Token' => $this->config['api_token'],
        ])->get('https://api.targetcircle.com/api/v1/transactions', $query);

        $this->logger->debug($query);
        $this->logger->debug($transactionsResponse->effectiveUri());

        $amount = 0.00;
        $transactionsData = Arr::get($transactionsResponse->throw()->json(), 'data', []);
        $this->logger->debug($transactionsData);

        foreach ($transactionsData as $transaction) {
            $amount += $transaction['commission'] + $transaction['payout'];
        }

        return $amount;
    }

    public function periodFrom(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    public function __toString(): string
    {
        return json_encode([
            'Name' => self::name(),
            'Marketing chanel id' => $this->marketingChannelId,
            'Market id' => $this->marketId,
            'Currency id' => $this->currencyId,
            'Api token' => $this->config['api_token'],
            'Offer id' => $this->config['offer_sid']
        ]);
    }

    public static function name(): string
    {
        return 'Performission';
    }
}
