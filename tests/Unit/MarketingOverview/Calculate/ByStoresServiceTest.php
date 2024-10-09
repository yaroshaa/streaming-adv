<?php


namespace Tests\Unit\MarketingOverview\Calculate;


use Carbon\Carbon;
use Modules\MarketingOverview\Services\Calculate\ByStoresService;
use PHPUnit\Framework\TestCase;

class ByStoresServiceTest extends TestCase
{
    public function testGettingStoresStatistic()
    {
        Carbon::setTestNow(Carbon::parse('2021-05-20T00:00:00+00:00'));
        $service = new ByStoresService(...$this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ByStoresServiceInput.json'), true);
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ByStoresServiceOutput.json'), true);
    }
}
