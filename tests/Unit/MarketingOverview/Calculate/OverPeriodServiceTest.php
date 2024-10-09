<?php

namespace Tests\Unit\MarketingOverview\Calculate;

use Modules\MarketingOverview\Services\Calculate\OverPeriodService;
use PHPUnit\Framework\TestCase;

class OverPeriodServiceTest extends TestCase
{
    public function testGettingExpenseSpendStatistic()
    {
        $service = new OverPeriodService(...$this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/OverPeriodServiceInput.json'), true);
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/OverPeriodServiceOutput.json'), true);
    }
}
