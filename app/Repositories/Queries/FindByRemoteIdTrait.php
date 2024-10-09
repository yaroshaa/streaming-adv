<?php

namespace App\Repositories\Queries;

/**
 * Trait FindByRemoteIdTrait
 * @package App\Repositories\Queries
 */
trait FindByRemoteIdTrait
{
    /**
     * @param $remoteId
     * @return mixed
     */
    public static function findByRemoteId($remoteId)
    {
        return static::get()->findOneBy([
            'remoteId' => $remoteId
        ]);
    }
}
