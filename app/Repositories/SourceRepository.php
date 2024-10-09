<?php

namespace App\Repositories;

use App\Entities\Source;
use App\Repositories\Queries\FindByRemoteIdTrait;
use App\Traits\RepositoryStaticAccess;

class SourceRepository extends ChildEntityRepository
{
    use RepositoryStaticAccess, FindByRemoteIdTrait;

    public static function getEntityModel(): string
    {
        return Source::class;
    }
}
