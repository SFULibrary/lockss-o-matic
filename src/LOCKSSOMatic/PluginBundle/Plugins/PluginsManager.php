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

namespace LOCKSSOMatic\PluginBundle\Plugins;

use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * PluginsManager maintains a list of plugins. It is a "compiled" class that 
 * Symfony processes to create the list of plugins. 
 * 
 * If this seems very abstract and hand-wavy, don't worry: it is. The class 
 * and the addPlugin method are registered in PluginCompilerPass and called
 * automatically by something deep in Symfony's inner workings. The PluginCompilerPass
 * object is created and registered with Symfony in the bundle's base class.
 */
class PluginsManager extends ContainerAware
{
    /**
     * @var AbstractPlugin
     */
    private $plugins;
    
    /**
     * Build a plugins manager.
     */
    public function __construct() {
        $this->plugins = array();
    }
    
    /**
     * Add a plugin to the manager.
     * 
     * @param AbstractPlugin $plugin
     */
    public function addPlugin(AbstractPlugin $plugin) {
        $this->plugins[] = $plugin;
    }

    /**
     * Get a list of the plugins.
     * 
     * @return AbstractPlugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

}
