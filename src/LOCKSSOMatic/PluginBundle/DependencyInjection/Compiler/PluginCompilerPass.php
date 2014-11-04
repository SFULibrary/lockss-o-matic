<?php

namespace LOCKSSOMatic\PluginBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PluginCompilerPass implements CompilerPassInterface {
    
    public function process(ContainerBuilder $container)
    {
        error_reporting(-1);
        
        $definition = $container->getDefinition('lomplugin.manager');
        $plugins = $container->findTaggedServiceIds('lomplugin.plugin');
        foreach($plugins as $id => $attributes) {
            $definition->addMethodCall('addPlugin', array(new Reference($id)));
        }
    }

}