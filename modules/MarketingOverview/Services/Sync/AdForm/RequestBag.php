<?php

namespace Modules\MarketingOverview\Services\Sync\AdForm;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestBag
{
    const API_DOMAIN = 'https://api.adform.com';
    const TTL_OF_TOKEN = (60 * 60) - 60; // 3600  - 60 +- IO lag on requests time
    const RETRY_ATTEMPTS = 5;
    const SUCCESS_STATUS_OPERATION = 'succeeded';

    private int $advertiserId;
    private string $clientId;
    private string $clientSecret;
    private array $config;
    private ?Logger $logger;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->advertiserId = $config['advertiser_id'];
    }

    private function log()
    {
        return $this->logger ?? Log::channel();
    }

    public function setLog(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @throws RequestException
     */
    public function listOfOperations(): array
    {
        return $this->sendGetRequest('/v1/buyer/stats/operations');
    }

    /**
     * @throws Exception
     */
    public function getAmount(): float
    {
        $accessToken = $this->getAccessToken();

        $requestOperationHeaders = $this->requestOperation($accessToken);
        $locationOfReport = Arr::get($requestOperationHeaders, 'Location.0', '');
        $statusLocation = Arr::get($requestOperationHeaders, 'Operation-Location.0', '');
        $this->log()->info([
            'location of report' => $locationOfReport,
            'location of status' => $statusLocation
        ]);
        $operationStatus = '';
        $operationId = '';
        try {
            for ($i = 1; $i <= self::RETRY_ATTEMPTS; $i++) {
                $operationStatusResponse = $this->sendGetRequest($statusLocation);
                $operationStatus = Arr::get($operationStatusResponse, 'status', '');
                $operationId = Arr::get($operationStatusResponse, 'id');
                $this->log()->info($operationStatusResponse);
                if ($operationStatus === self::SUCCESS_STATUS_OPERATION) {
                    break;
                }
                sleep($i); // 1 sec, 2 sec, ...5 sec for next requests
            }


            if ($operationStatus !== self::SUCCESS_STATUS_OPERATION) {
                throw new Exception('Maximum retry attempts of check report status');
            }

            $reportData = $this->sendGetRequest($locationOfReport);
            $this->log()->debug($reportData);
            $amount = Arr::get($reportData, 'reportData.rows.0.3');
            if (!is_numeric($amount)) {
                throw new Exception('Amount is not numeric value');
            }

            return $amount;
        } catch (Exception $exception) {
            $this->deleteOperation($operationId);
            throw new Exception($exception);
        }
    }

    /**
     * @throws RequestException
     */
    private function getAccessToken(): string
    {
        if (Cache::has($this->cacheTokenKey())) {
            return Cache::get($this->cacheTokenKey());
        }

        $tokenRequest = Http::asForm()->post('https://id.adform.com/sts/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://api.adform.com/scope/buyer.stats https://api.adform.com/scope/buyer.campaigns.api.readonly'
        ])->throw()->json();

        $accessToken = Arr::get($tokenRequest, 'access_token', '');

        Cache::put($this->cacheTokenKey(), $accessToken, self::TTL_OF_TOKEN);

        return $accessToken;
    }

    private function cacheTokenKey(): string
    {
        return 'adform.' . $this->clientId . '.access_token';
    }

    /**
     * @throws RequestException
     */
    private function requestOperation($token): array
    {
        return Http::withToken($token)
            ->withBody(json_encode($this->getBodyParams()), 'application/json')
            ->post(self::API_DOMAIN . '/v1/buyer/stats/data')
            ->throw()
            ->headers();
    }

    private function getBodyParams(): array
    {
        $from = Carbon::now($this->config['timezone'])->startOfDay();
        $to = Carbon::now($this->config['timezone'])->startOfDay()->addDay();

        return [
            "dimensions" => [
                "date",
                "client",
            ],
            "metrics" => [
                [
                    "metric" => "cost"
                ],
                [
                    "metric" => "cost"
                ],
                [
                    "metric" => "impressions",
                    "specs" => [
                        "adUniqueness" => "campaignUnique"
                    ]
                ],
                [
                    "metric" => "clicks",
                    "specs" => [
                        "adUniqueness" => "campaignUnique"
                    ]
                ]
            ],
            "filter" => [
                "date" => [
                    "from" => $from->toDateTimeLocalString(),
                    "to" => $to->toDateTimeLocalString(),
                ],
                "client" => [
                    'id' => $this->advertiserId,
                ]
            ],
            "paging" => [
                "offset" => 0,
                "limit" => 3000
            ],
            "includeRowCount" => true,
            "includeTotals" => true,
            "sort" => [
                [
                    "dimension" => "date",
                    "direction" => "desc"
                ],
                [
                    "metric" => "impressions",
                    "specs" => [
                        "adUniqueness" => "campaignUnique"
                    ],
                    "direction" => "asc"
                ]
            ]
        ];
    }

    /**
     * @throws RequestException
     */
    private function sendGetRequest($url)
    {
        return Http::withToken($this->getAccessToken())->get(self::API_DOMAIN . $url)->throw()->json();
    }

    /**
     * @throws RequestException
     */
    public function deleteOperation($id): void
    {
        Http::withToken($this->getAccessToken())
            ->delete('https://api.adform.com/v1/buyer/stats/data/' . $id)
            //->throw()
            ->json();
    }
}
