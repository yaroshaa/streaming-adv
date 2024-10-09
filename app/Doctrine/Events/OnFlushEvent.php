<?php

namespace App\Doctrine\Events;

use Doctrine\ORM\Event\OnFlushEventArgs;

class OnFlushEvent
{
    public function onFlush(?OnFlushEventArgs $eventArgs = null)
    {
        if ($eventArgs === null) {
            return;
        }

        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            event('Doctrine.Insert.' . class_basename($entity));
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            event('Doctrine.Update.' . class_basename($entity));
        }
    }
}
