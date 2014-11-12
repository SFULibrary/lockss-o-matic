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

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\PluginBundle\Entity\LomPluginData;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Yaml;

/**
 * Base class for plugins.
 * 
 * Plugins are defined by their plugin.yml file which tags the class as a plugin
 * and lists the events the plugin responds to.
 */
abstract class AbstractPlugin extends ContainerAware
{

    private $pluginId;

    /**
     * Get the name of the plugin.
     * 
     * @return string
     */
    abstract function getName();

    /**
     * Get a description of the plugin.
     * 
     * @return string
     */
    abstract function getDescription();

    /**
     * Array built from parsing the settings.yml file for the
     * plugin, if it exists.
     *
     * @var array
     */
    private $settings;

    public function __construct()
    {
        $classInfo = new ReflectionClass($this);
        $dir = dirname($classInfo->getFileName());
        if(file_exists("$dir/settings.yml")) {
            $this->settings = Yaml::parse("$dir/settings.yml");
        }
    }

    public function getSetting($name)
    {
        if ($this->settings === null) {
            return null;
        }
        if (!array_key_exists($name, $this->settings)) {
            return null;
        }
        return $this->settings[$name];
    }

    public function setPluginId($pluginId)
    {
        $this->pluginId = $pluginId;
    }

    /**
     * 
     * @param type $object
     * @param type $key
     * @return LomPluginData
     */
    private function getDataObject($key, $object = null)
    {
        $domain = null;
        $objectId = null;
        if ($object !== null && is_object($object)) {
            $domain = get_class($object);
            $objectId = $object->getId();
        } else if ($object !== null && is_string($object)) {
            $domain = $object;
        }

        /** @var EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        $repo = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $data = $repo->findOneBy(array(
            'plugin'   => get_class($this),
            'domain'   => $domain,
            'objectId' => $objectId,
            'datakey'  => $key,
        ));
        return $data;
    }

    public function getPluginId()
    {
        return $this->pluginId;
    }

    public function getData($key, $object = null)
    {
        $data = $this->getDataObject($key, $object);
        if ($data === null) {
            return null;
        }
        $value = $data->getValue();
        $res = unserialize($value);
        return $res;
    }

    public function setData($key, $object = null, $value = null)
    {
        $data = $this->getDataObject($object, $key);
        if ($data === null) {
            $data = new LomPluginData();
            $this->container->get('doctrine')->getManager()->persist($data);
            $data->setPlugin(get_class($this));
            $data->setDataKey($key);
            if ($object !== null && is_object($object)) {
                $data->setDomain(get_class($object));
                $data->setObjectId($object->getId());
            } else if ($object !== null && is_string($object)) {
                $data->setDomain($object);
            }
        }
        $data->setValue(serialize($value));
        $this->container->get('doctrine')->getManager()->flush();
    }

    public function __toString()
    {
        return $this->getName();
    }

}
