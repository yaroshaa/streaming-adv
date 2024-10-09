<?php

namespace App\Repositories;

use App\Entities\AnalyticsSite;
use App\Repositories\Queries\FindByRemoteIdTrait;
use App\Traits\RepositoryStaticAccess;

/**
 * Class AnalyticsSiteRepository
 * @package App\Repositories
 */
class AnalyticsSiteRepository extends ChildEntityRepository
{
    use RepositoryStaticAccess, FindByRemoteIdTrait;

    public static function getEntityModel(): string
    {
        return AnalyticsSite::class;
    }
}
