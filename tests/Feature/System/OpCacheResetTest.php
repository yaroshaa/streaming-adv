<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class OpCacheResetTest extends TestCase
{
    const ENTRY_POINT = 'api/system/op-cache-reset/{secret}';

    public function testWithoutSecret()
    {
        $this->get($this->getUrl())->assertNotFound();
    }

    public function testInvalidSecret()
    {
        $this->get($this->getUrl(12345))->assertForbidden();
    }

    public function testValidSecret()
    {
        $this->get($this->getUrl('95b3f901151d2810a02da40a1b6c4e6da628ed6c3f392ed82963e4ccc62f931a'))->assertOk();
        if (function_exists('opcache_reset')) {
            $this->get($this->getUrl('95b3f901151d2810a02da40a1b6c4e6da628ed6c3f392ed82963e4ccc62f931a'))->assertJson([
                'Result' => 'No' // In cli mode opcache_reset will be returned false
            ]);
        } else {
            $this->get($this->getUrl('95b3f901151d2810a02da40a1b6c4e6da628ed6c3f392ed82963e4ccc62f931a'))->assertJson([
                'Result' => 'Function "opcache_reset" not exist'
            ]);
        }
    }

    private function getUrl(string $secret = '')
    {
        return str_replace('{secret}', $secret, self::ENTRY_POINT);
    }
}
