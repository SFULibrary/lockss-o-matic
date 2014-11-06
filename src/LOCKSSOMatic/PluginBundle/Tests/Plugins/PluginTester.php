<?php

namespace LOCKSSOMatic\PluginBundle\Tests\Plugins;

use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;

class PluginTester extends AbstractPlugin
{

    public function getDescription()
    {
        return "Simple plugin for testing get/set data.";
    }

    public function getName()
    {
        return "PluginTester";
    }

}
