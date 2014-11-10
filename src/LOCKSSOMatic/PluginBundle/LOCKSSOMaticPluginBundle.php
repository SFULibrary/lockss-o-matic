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

namespace LOCKSSOMatic\PluginBundle;

use LOCKSSOMatic\PluginBundle\DependencyInjection\Compiler\PluginCompilerPass;
use LOCKSSOMatic\PluginBundle\DependencyInjection\LOCKSSOMaticPluginExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Events and plugins to listen for the events and respond appropriately. Add 
 * plugins to the Plugins directory.
 */
class LOCKSSOMaticPluginBundle extends Bundle
{
    /**
     * This bundle uses a non-standard extension, so declare it here.
     * 
     * @return LOCKSSOMaticPluginExtension
     */
    public function getContainerExtension()
    {
        return new LOCKSSOMaticPluginExtension();
    }
    
    /**
     * This bundle jumps through an extra hoop for plugins, so get the
     * compiler set up and declared here.
     * 
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PluginCompilerPass());
    }
}
