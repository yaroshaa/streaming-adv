<?php

namespace Modules\StoreToday\Services;

use Carbon\Carbon;
use Modules\StoreToday\ClickHouse\Repositories\StoreTodayRepository;

class ProfitService
{
    private StoreTodayRepository $repository;
    private Carbon $from;
    private Carbon $to;
    private int $currencyId;

    public function __construct(StoreTodayRepository $repository, Carbon $from, Carbon $to, int $currencyId)
    {
        $this->repository = $repository;
        $this->from = $from;
        $this->to = $to;
        $this->currencyId = $currencyId;
    }

    public function getProfit(): array
    {
        return $this->repository->getProfit($this->from, $this->to, $this->currencyId);
    }

    public function getProfitForecast(): array
    {
        return $this->repository->getProfit($this->from->clone()->subDay(), $this->from, $this->currencyId);
    }

    public function get30DaysProfit(): array
    {
        return $this->repository->getProfit($this->from->clone()->subDays(30), $this->to, $this->currencyId);
    }

    public function get30DaysProfitForecast(): array
    {
        return $this->repository->getProfit(
            $this->from->subDays(60),
            $this->from->clone()->subDays(30)->setHours(23)->setMinutes(59),
            $this->currencyId
        );
    }
}
