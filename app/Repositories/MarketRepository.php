<?php

namespace App\Repositories;

use App\Entities\Market;
use App\Repositories\Queries\FindByRemoteIdTrait;
use App\Traits\RepositoryStaticAccess;

class MarketRepository extends ChildEntityRepository
{
    use RepositoryStaticAccess, FindByRemoteIdTrait;

    public static function getEntityModel(): string
    {
        return Market::class;
    }
}
