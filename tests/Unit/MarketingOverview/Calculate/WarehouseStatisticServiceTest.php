<?php

namespace Tests\Unit\MarketingOverview\Calculate;

use Modules\MarketingOverview\Services\Calculate\WarehouseStatisticService;
use PHPUnit\Framework\TestCase;

class WarehouseStatisticServiceTest extends TestCase
{
    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/WarehouseStatisticInput.json'), true);
    }

    public function testGettingWarehouseStatistic()
    {
        $service = new WarehouseStatisticService($this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/WarehouseStatisticOutput.json'), true);
    }
}
