<?php

namespace Modules\MarketingOverview\Services\Sync\Snapchat;

use Closure;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestBag
{
    private string $token;
    private string $refreshToken;
    private string $clientId;
    private string $clientSecret;
    private string $adAccountId;
    private string $timezone;

    public function __construct(array $config, string $timezone)
    {
        $this->token = $config['access_token'];
        $this->refreshToken = $config['refresh_token'];
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->adAccountId = $config['ad_account_id'];
        $this->timezone = $timezone;
    }

    /**
     * @throws Exception
     */
    public function getCampaignIds(): array
    {
        $response = $this->call(fn() => $this->campaignRequest());
        return Arr::pluck($response['campaigns'], 'campaign.id');
    }

    /**
     * Simple handling request errors unauthorized/too many;
     * @throws Exception
     */
    public function call(Closure $closure)
    {
        try {
            return $closure()->throw()->json();
        } catch (Exception $exception) {
            Log::error($exception);
            if ($exception->getCode() === \Illuminate\Http\Response::HTTP_UNAUTHORIZED) {
                $this->refreshToken();
                Log::warning('Refresh token');
                return $closure()->throw()->json();
            }

            if ($exception->getCode() === \Illuminate\Http\Response::HTTP_TOO_MANY_REQUESTS) {
                sleep(10);
                Log::error('Too many request, try to retry');
                return $closure()->throw()->json();
            }
        }
        throw new Exception('Unhandled exception');
    }

    /**
     * @throws RequestException
     */
    public function refreshToken()
    {
        $response = Http::asForm()->post('https://accounts.snapchat.com/login/oauth2/access_token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getRefreshToken(),
        ])->throw()->json();

        $this->setToken($response['access_token']);
        $this->setRefreshToken($response['refresh_token']);
    }

    private function getRefreshToken()
    {
        if (Cache::has($this->cacheRefreshTokenKey())) {
            return Cache::get($this->cacheRefreshTokenKey());
        }

        return $this->refreshToken;
    }

    private function cacheRefreshTokenKey(): string
    {
        return 'snapchat.' . $this->clientId . '.refresh_token';
    }

    private function setToken($token): void
    {
        Cache::put($this->cacheTokenKey(), $token, 1800); // 1800 from snapchat docs (irl 1750+-)
    }

    private function cacheTokenKey(): string
    {
        return 'snapchat.' . $this->clientId . '.token';
    }

    private function setRefreshToken($refreshToken): void
    {
        Cache::put($this->cacheRefreshTokenKey(), $refreshToken);
    }

    public function campaignRequest(): Response
    {
        return Http::withToken($this->getToken())->get($this->buildUrl('adaccounts', $this->adAccountId, 'campaigns'));
    }

    private function getToken()
    {
        if (Cache::has($this->cacheTokenKey())) {
            return Cache::get($this->cacheTokenKey());
        }

        return $this->token;
    }

    /**
     * @param string $entity
     * @param string $uuid
     * @param string $action
     * @return array|string|string[]
     */
    public function buildUrl(string $entity, string $uuid, string $action): string
    {
        return str_replace([
            '{entity}',
            '{uuid}',
            '{action}'
        ], [
            $entity,
            $uuid,
            $action
        ], 'https://adsapi.snapchat.com/v1/{entity}/{uuid}/{action}');
    }

    /**
     * @throws Exception
     */
    public function getCampaignSpend(string $campaignId): array
    {
        $response = $this->call(fn() => $this->campaignSpendRequest($campaignId));
        return Arr::pluck($response['total_stats'], 'total_stat.stats.spend');
    }

    /**
     * @param string $campaignId
     * @return Response
     */
    public function campaignSpendRequest(string $campaignId): Response
    {
        $currentDay = (new Carbon($this->timezone))
            ->startOfDay();

        $nextDay = (new Carbon($this->timezone))
            ->startOfDay()
            ->addDay();

        return Http::withToken($this->getToken())
            ->get($this->buildUrl('campaigns', $campaignId, 'stats'), [
                'granularity' => 'TOTAL',
                'start_time' => $currentDay->toIso8601String(),
                'end_time' => $nextDay->toIso8601String(),
                'fields' => 'impressions,spend,swipes'
            ]);
    }
}
