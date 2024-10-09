<?php

namespace Tests\Unit\MarketingOverview\Calculate;

use Modules\MarketingOverview\Services\Calculate\ConversationIndicatorService;

class ConversationIndicatorServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingConversationIndicatorStatistic()
    {
        $service = new ConversationIndicatorService(...$this->getInput());
        $this->assertEquals($this->getOutput(), $service->getStatistic());
    }

    private function getInput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ConversationIndicatorServiceInput.json'), true);
    }

    private function getOutput(): array
    {
        return json_decode(file_get_contents('storage/tests/Unit/MarketingOverview/Calculate/ConversationIndicatorServiceOutput.json'), true);
    }
}
