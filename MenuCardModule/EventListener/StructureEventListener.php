<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use HetBonteHert\Module\MenuCard\Entity\MenuPage;
use HetBonteHert\Module\MenuCard\Module\MenuPageModule;
use HetBonteHert\Module\MenuCard\Repository\MenuPageRepository;
use Tidi\Cms\Module\Core\Entity\Structure;

final class StructureEventListener
{
    /**
     * @var bool
     */
    private $needsFlush;

    /**
     * @var MenuPageRepository
     */
    private $menuPageRepository;

    public function __construct(MenuPageRepository $menuPageRepository)
    {
        $this->menuPageRepository = $menuPageRepository;
    }

    /**
     * Insert a new Page entity when a structure of that type is created.
     *
     * @param LifecycleEventArgs $event
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function postPersist(LifecycleEventArgs $event): void
    {
        /** @var Structure $object */
        $object = $event->getObject();

        if (!$object instanceof Structure) {
            return;
        }

        $script = $object->getModuleScript();

        if (MenuPageModule::MODULE_SCRIPT !== $script) {
            return;
        }

        $menuPage = (new MenuPage($object))
            ->setModifiedBy($object->getCreatedBy());

        $event->getObjectManager()->persist($menuPage);
        $this->needsFlush = true;
    }

    /**
     * Remove all related menuPage entities upon structure deletion.
     *
     * @param LifecycleEventArgs $event
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function preRemove(LifecycleEventArgs $event): void
    {
        /** @var Structure $object */
        $object = $event->getObject();

        if (!$object instanceof Structure) {
            return;
        }

        $script = $object->getModuleScript();

        if (MenuPageModule::MODULE_SCRIPT !== $script) {
            return;
        }

        $pageEntities = $this->menuPageRepository->findBy(['structure' => $object]);

        foreach ($pageEntities as $menuPage) {
            $event->getObjectManager()->remove($menuPage);
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        if (true === $this->needsFlush) {
            $this->needsFlush = false;
            $eventArgs->getObjectManager()->flush();
        }
    }
}
