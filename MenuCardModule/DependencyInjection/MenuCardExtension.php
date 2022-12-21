<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\DependencyInjection;

use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class MenuCardExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!is_array($bundles)) {
            throw new InvalidArgumentException('kernel.bundles should be an array');
        }

        $this->addMigrationConfig($container, $bundles);
    }

    /**
     * @param ContainerBuilder $container
     * @param string[]         $bundles
     */
    public function addMigrationConfig(ContainerBuilder $container, array $bundles): void
    {
        if (isset($bundles['TidiMigrationModuleBundle'])) {
            $migrationConfig = [
                'paths' => [
                    'migrations' => [
                        'HetBonteHert\\Module\\MenuCard' => __DIR__.'/../Migrations',
                    ],
                ],
            ];

            $container->prependExtensionConfig('tidi_migration_module', $migrationConfig);
        }
    }
}
