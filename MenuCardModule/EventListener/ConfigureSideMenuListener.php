<?php

namespace HetBonteHert\Module\MenuCard\EventListener;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tidi\Cms\Module\Admin\Event\ConfigureSideMenuEvent;
use Tidi\Cms\Module\Admin\Event\Events;

final class ConfigureSideMenuListener implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TranslatorInterface $translator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->translator           = $translator;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function onSideMenuConfigure(ConfigureSideMenuEvent $event): void
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $subMenu = $event->getMenu()
                ->addChild(
                    $this->translator->trans('menucard_module.admin.menucard', ['%count%' => 10]),
                    [
                        'route'           => '_menu_card.admin.menu_card_index',
                    ]
                )
                ->setAttribute('icon', 'image');
            $subMenu->addChild(
                $this->translator->trans('menucard_module.admin.menucard', ['%count%' => 10]),
                [
                    'route'           => '_menu_card.admin.menu_card_index',
                ]
            );

            $subMenu->addChild(
                $this->translator->trans('menucard_module.admin.category', ['%count%' => 10]),
                [
                    'route'           => '_menu_card.admin.category_index',
                ]
            );

            $subMenu->addChild(
                $this->translator->trans('menucard_module.admin.dish', ['%count%' => 10]),
                [
                    'route'           => '_menu_card.admin.dish_index',
                ]
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [Events::SIDE_MENU_CONFIGURE_SETTINGS => ['onSideMenuConfigure', 115]];
    }
}
