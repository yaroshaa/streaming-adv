<?php

namespace Modules\MarketingOverview\Services\Sync;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FacebookAdsSyncService extends BaseSyncService implements MarketingSyncServiceInterface
{
    public string $token;
    public string $actId;

    public static function name(): string
    {
        return 'Facebook';
    }

    public static function alias(): string
    {
        return 'facebook';
    }

    public function periodFrom(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    public function init()
    {
        if (empty($this->config['token'])) {
            throw new Exception("Token not exist");
        }

        if (empty($this->config['act_id'])) {
            throw new Exception("Act id not exist");
        }

        $this->token = $this->config['token'];
        $this->actId = $this->config['act_id'];
    }

    public function getAmount(): float
    {
        $json = Http::get('https://graph.facebook.com/v10.0/act_' . $this->actId . '/insights', [
            'fields' => 'spend',
            'date_preset' => 'today',
            'access_token' => $this->token
        ])->throw()->json();

        $correctPeriodFrom = $this->periodFrom()->format('Y-m-d');
        // If report by another period
        if (($reportPeriodFrom = Arr::get($json, 'data.0.date_start', false)) !== $correctPeriodFrom) {
            $this->logger->debug(
                sprintf(
                    'Date of report cannot be valid, excepted %s, got %s.',
                    $correctPeriodFrom,
                    $reportPeriodFrom
                )
            );

            return 0.00;
        }

        $this->logger->debug($json);

        return Arr::get($json, 'data.0.spend', 0.00);
    }

    public function __toString(): string
    {
        return json_encode([
            'Name' => self::name(),
            'Marketing chanel id' => $this->marketingChannelId,
            'Market id' => $this->marketId,
            'Currency id' => $this->currencyId,
            'Act id' => $this->actId
        ]);
    }

    public static function getIntervalOnSeconds(): int
    {
        return 60 * 5;
    }
}
