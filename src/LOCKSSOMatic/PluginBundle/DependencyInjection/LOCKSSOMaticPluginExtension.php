<?php

namespace LOCKSSOMatic\PluginBundle\DependencyInjection;

use RecursiveIteratorIterator;
use RegexIterator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use RecursiveDirectoryIterator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LOCKSSOMaticPluginExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $pluginLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../'));

        // find the plugin.yml files and load them.
        $directoryIterator = new RecursiveDirectoryIterator(__DIR__ . '/../../');
        $flattenedIterator = new RecursiveIteratorIterator($directoryIterator);
        $ymlFiles = new RegexIterator($flattenedIterator, '/plugin\.yml$/');

        foreach($ymlFiles as $yml) {
            $pluginLoader->load($yml->getPath() . '/' . $yml->getFileName());
        }

    }
}
