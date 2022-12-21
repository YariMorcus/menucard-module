<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use HetBonteHert\Module\MenuCard\Entity\Category;
use HetBonteHert\Module\MenuCard\Exception\CategoryInUseException;

class CategoryListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LifecycleEventArgs $event): void
    {
        /** @var Category $category */
        $category = $event->getObject();

        if (!$category instanceof Category) {
            return;
        }

        if ($category->getDishes()->count()) {
            throw new CategoryInUseException();
        }
    }
}
