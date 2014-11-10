<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\PluginBundle\DependencyInjection;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration. Since 
 * the plugins are defined as services, and since they don't follow the standard
 * service.yml configuration file naming convention, they must be loaded here.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LOCKSSOMaticPluginExtension extends Extension
{
    /**
     * Find and load all the plugin definitions.
     * 
     * {@inheritdocs}
     */
    public function load(array $configs, ContainerBuilder $container)
    {        
        // load all the plugins as services here.        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        // Load this bundle's services.yml file before loading the plugin 
        // service definitions.
        $svcLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $svcLoader->load('services.yml');

        
        $pluginLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Plugins/'));

        // find the plugin.yml files and load them.
        $directoryIterator = new RecursiveDirectoryIterator(__DIR__ . '/../Plugins');
        $flattenedIterator = new RecursiveIteratorIterator($directoryIterator);
        $ymlFiles = new RegexIterator($flattenedIterator, '/plugin\.yml$/');
        
        foreach($ymlFiles as $yml) {            
            $pluginLoader->load($yml->getPath() . '/' . $yml->getFileName());
        }
        
    }
    
}
