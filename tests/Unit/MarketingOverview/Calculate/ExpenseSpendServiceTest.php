<?php

namespace Tests\Unit\MarketingOverview\Calculate;

use Modules\MarketingOverview\Services\Calculate\ExpenseSpendService;
use PHPUnit\Framework\TestCase;

class ExpenseSpendServiceTest extends TestCase
{
    public function testGettingExpenseSpendStatistic()
    {
        $service = new ExpenseSpendService(...$this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ExpenseSpendServiceInput.json'), true);
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ExpenseSpendServiceOutput.json'), true);
    }
}
