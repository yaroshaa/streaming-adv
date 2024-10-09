<?php

namespace Modules\Feedbacks\Facades;

use Modules\Feedbacks\Services\HelpScout\HelpScoutService;
use Illuminate\Support\Facades\Facade;

/**
 * Class HelpScout
 * @package Modules\Facebook\Facades
 */
class HelpScout extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return HelpScoutService::class;
    }
}
