<?php


namespace Modules\Feedbacks\Services\Facebook\Api;

use Carbon\Carbon;
use FacebookAds\Api;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Class DataProvider
 * @package Modules\Feedbacks\Services\Facebook
 */
class DataProvider
{
    const DEFAULT_CACHE_TTL = 60 * 60 * 3; // 3 hours

    private Api $api;
    private string $path;
    private int $limit = 1000;
    private array $fields = [];
    private array $params = [];
    private ?string $next = null;

    private bool $enableCache = false;
    private int $cacheTTLSeconds = self::DEFAULT_CACHE_TTL;

    /**
     * DataProvider constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if ($this->enableCache) {
            if (Cache::has($this->getCacheKey())) {
                return Cache::get($this->getCacheKey());
            } else {
                $data = $this->getDataFromApi();
                Cache::put($this->getCacheKey(), $data, Carbon::now()->addSeconds($this->cacheTTLSeconds));
                return $data;
            }
        }

        return $this->getDataFromApi();
    }

    /**
     * Getting all data without pagination
     * @return array
     */
    private function getDataFromApi(): array
    {
        $data = [];

        do {
            $content = $this->api->call($this->buildQueryString())->getContent();
            $data = array_merge($data, $content['data']);
            $this->setNext(Arr::has($content, ['paging', 'paging.next']) ? Arr::get($content, 'paging.cursors.after') : null);
        } while ($this->next !== null);

        return $data;
    }

    /**
     * @return string
     */
    private function buildQueryString(): string
    {
        $params = array_replace([
            'limit' => $this->limit,
        ], $this->params);

        if ($this->next !== null) {
            $params['after'] = $this->next;
        }

        return $this->path . '?' . Arr::query(Arr::add($params, 'fields', implode(',', $this->fields)));
    }

    private function getCacheKey(): string
    {
        return $this->path . hash('sha256', serialize($this->buildQueryString()));
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): DataProvider
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields): DataProvider
    {
        $this->fields = $fields;
        return $this;
    }

    public function setParams(array $params): DataProvider
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): DataProvider
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string|null $next
     * @return $this
     */
    public function setNext(?string $next): DataProvider
    {
        $this->next = $next;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNext(): ?string
    {
        return $this->next;
    }

    /**
     * @param int|float $ttl
     * @return $this
     */
    public function withCache(int $ttl = self::DEFAULT_CACHE_TTL): DataProvider
    {
        $this->enableCache = true;
        $this->cacheTTLSeconds = $ttl;
        return $this;
    }
}
