<?php

namespace HetBonteHert\Module\MenuCard\Module;

use Tidi\Cms\Module\Core\Module\InstallableModuleInterface;
use Tidi\Cms\Module\Core\Module\NodeInterface;

final class MenuPageModule implements NodeInterface, InstallableModuleInterface
{
    public const MODULE_SCRIPT = 'MenuPage';

    /**
     * Returns the modules script.
     *
     * @return string
     */
    public function getModuleScript(): string
    {
        return static::MODULE_SCRIPT;
    }

    /**
     * Returns true if the current module has multinode capabilities.
     *
     * @return bool
     */
    public function isMultiNode(): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function getModuleConfiguration(): array
    {
        return [
            'active'     => false,
            'name'       => 'Menu pagina',
            'script'     => self::MODULE_SCRIPT,
            'amount'     => 0,
            'editable'   => true,
            'children'   => true,
            'bundle'     => 'MenuCardBundle',
            'adminRoute' => '_menu_card.admin.menu_page_',
        ];
    }
}
