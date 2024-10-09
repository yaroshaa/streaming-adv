<?php

namespace Tests\Unit\MarketingOverview\Calculate;

use Carbon\Carbon;
use Exception;
use Modules\MarketingOverview\Services\Calculate\TotalService;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;

class TotalServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testTotalsStatistic()
    {
        Carbon::setTestNow(Carbon::parse('2021-05-20'));
        Config::shouldReceive('get')
            ->with('threshold-values.marketing_overview.cm_ratio.value')
            ->andReturn(50);
        Config::shouldReceive('get')
            ->with('threshold-values.marketing_overview.cm_ratio.position')
            ->andReturn('start');
        Config::shouldReceive('get')
            ->with('threshold-values.marketing_overview.spend_ratio.value')
            ->andReturn(70);
        Config::shouldReceive('get')
            ->with('threshold-values.marketing_overview.spend_ratio.position')
            ->andReturn('end');

        $service = new TotalService(...$this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/TotalServiceInput.json'), true);
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/TotalServiceOutput.json'), true);
    }
}
