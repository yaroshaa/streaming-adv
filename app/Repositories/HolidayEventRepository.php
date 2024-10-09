<?php

namespace App\Repositories;

use App\Entities\HolidayEvent;
use App\Traits\RepositoryStaticAccess;

class HolidayEventRepository extends ChildEntityRepository
{
    use RepositoryStaticAccess;

    public static function getEntityModel(): string
    {
        return HolidayEvent::class;
    }

    public function getHolidayEvent()
    {
        return $this->createQueryBuilder('p')
            ->where('p.date > CURRENT_TIMESTAMP()')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
