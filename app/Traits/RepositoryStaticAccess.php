<?php

namespace App\Traits;

use EntityManager;

/**
 * Trait RepositoryStaticAccess
 * @package App\Traits
 */
trait RepositoryStaticAccess
{
    /**
     * @return string
     */
    public static function getEntityModel(): string
    {
        return '';
    }

    /**
     * @return static
     */
    public static function get()
    {
        return EntityManager::getRepository(static::getEntityModel());
    }
}
