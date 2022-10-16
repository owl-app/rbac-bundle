<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class OwlRbacExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('owl', $config['driver'], $config['resources'] ?? [], $container);

        $loader->load('services.xml');

        $loader->load(sprintf('integrations/%s.xml', $config['driver']));

        $container->setParameter('owl.rbac.auth_item_class', $config['auth_item_class']);
    }
}
