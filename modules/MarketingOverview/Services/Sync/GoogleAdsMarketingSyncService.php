<?php

namespace Modules\MarketingOverview\Services\Sync;

use Exception;
use Carbon\Carbon;
use Google\Ads\GoogleAds\Lib\Configuration;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V6\GoogleAdsClientBuilder;

/**
 * Class GoogleAdsMarketingSyncService
 * @package Modules\MarketingOverview\Services\Sync
 */
class GoogleAdsMarketingSyncService extends BaseSyncService implements MarketingSyncServiceInterface
{
    /**
     * @return string
     */
    public static function alias(): string
    {
        return 'google';
    }

    public function periodFrom(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    public static function getIntervalOnSeconds(): int
    {
        return 60;
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        if (empty($this->config['developerToken'])) {
            throw new Exception("Developer token not exist");
        }

        if (empty($this->config['loginCustomerId'])) {
            throw new Exception("Login customer id (MMC Account) not exist");
        }

        if (empty($this->config['account_id'])) {
            throw new Exception("Account id not exist");
        }

        if (empty($this->config['clientId'])) {
            throw new Exception("Client id not exist");
        }

        if (empty($this->config['clientSecret'])) {
            throw new Exception("Client secret not exist");
        }

        if (empty($this->config['refreshToken'])) {
            throw new Exception("Refresh token not exist");
        }
    }

    public function getAmount(): float
    {
        $config = [
            'GOOGLE_ADS' => [
                'developerToken' => $this->config['developerToken'],
                'loginCustomerId' => $this->config['loginCustomerId'],
            ],
            'OAUTH2' => [
                'clientId' => $this->config['clientId'],
                'clientSecret' => $this->config['clientSecret'],
                'refreshToken' => $this->config['refreshToken']
            ]
        ];

        $configuration = new Configuration($config);
        $googleAdsServiceClient = (new GoogleAdsClientBuilder())
            ->from($configuration)
            ->withOAuth2Credential((new OAuth2TokenBuilder())
                ->from($configuration)
                ->build())
            ->build();

        $googleAdsServiceClient = $googleAdsServiceClient->getGoogleAdsServiceClient();

        // Creates a query that retrieves campaigns.
        $query =
            "SELECT campaign.id, "
            . "campaign.name, "
            . "segments.date, "
            . "metrics.cost_micros "
            . "FROM campaign "
            . "WHERE segments.date DURING TODAY "
            . " "
            . "ORDER BY segments.date DESC";

        // Issues a search request by specifying page size.
        $response =
            $googleAdsServiceClient->search($this->config['account_id'], $query, ['pageSize' => 1000]);

        // Iterates over all rows in all pages and extracts the information.
        $costMicros = 0;
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            $costMicros += $googleAdsRow->getMetrics()->getCostMicros();
            $this->logger->debug([
                'name' => $googleAdsRow->getCampaign()->getName(),
                'cost' => $googleAdsRow->getMetrics()->getCostMicros(),
                'date' => $googleAdsRow->getSegments()->getDate(),
            ]);
        }
        return $costMicros / 1000000;
    }

    public function __toString(): string
    {
        return json_encode([
            'Name' => self::name(),
            'Marketing chanel id' => $this->marketingChannelId,
            'Market id' => $this->marketId,
            'Currency id' => $this->currencyId,
            'Manager id' => $this->config['loginCustomerId'],
            'Account id' => $this->config['account_id']
        ]);
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Google';
    }
}
