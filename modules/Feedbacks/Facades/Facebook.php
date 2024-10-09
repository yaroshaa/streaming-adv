<?php

namespace Modules\Feedbacks\Facades;

use Modules\Feedbacks\Services\Facebook\Api\DataProvider;
use Modules\Feedbacks\Services\Facebook\FacebookService;
use Illuminate\Support\Facades\Facade;

/**
 * Class Facebook
 * @method DataProvider getFacebookDataProvider()
 * @package Modules\Facebook\Facades
 */
class Facebook extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return FacebookService::class;
    }
}
