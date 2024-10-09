<?php

namespace App\Repositories;

use App\Entities\MarketingChannel;
use App\Traits\RepositoryStaticAccess;

class MarketingChannelRepository extends ChildEntityRepository
{
    use RepositoryStaticAccess;

    public static function getEntityModel(): string
    {
        return MarketingChannel::class;
    }
}
