<?php

namespace LOCKSSOMatic\PluginBundle;

use LOCKSSOMatic\PluginBundle\DependencyInjection\Compiler\PluginCompilerPass;
use LOCKSSOMatic\PluginBundle\DependencyInjection\LOCKSSOMaticPluginExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LOCKSSOMaticPluginBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LOCKSSOMaticPluginExtension();
    }
    
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PluginCompilerPass());
    }
}
