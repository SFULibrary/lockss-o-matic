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

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use LOCKSSOMatic\CrudBundle\Entity\PluginProperty;

/**
 * Load some  plugin property test data into the database.
 */
class LoadPluginPropertyTestData extends AbstractDataFixture implements OrderedFixtureInterface
{

    protected function doLoad(ObjectManager $em)
    {
        /** @var Plugin $plugin */
        $plugin = $this->getReference('plugin');
        $this->setReference(
            'prop_name',
            $this->buildPluginProperty($em, $plugin, 'plugin_name', 'Test plugin 1')
        );
        $this->buildPluginProperty($em, $plugin, 'plugin_version', 'test beta');
        $this->buildPluginProperty($em, $plugin, 'plugin_identifier', 'ca.sfu.test');
        $this->buildPluginProperty($em, $plugin, 'au_start_url', '"%s/lockss/%d", base_url, year');

        $config = $this->buildPluginProperty($em, $plugin, 'plugin_config_props');
        $this->setReference('prop_config', $config);

        $cp1 = $this->buildPluginProperty($em, $plugin, 'configparamdescr', null, $config);
        $this->buildPluginProperty($em, $plugin, 'key', 'year', $cp1);
        $this->buildPluginProperty($em, $plugin, 'definitional', 'false', $cp1);
        $this->setReference('plugin_configparamdescr', $cp1);

        $cp2 = $this->buildPluginProperty($em, $plugin, 'configparamdescr', null, $config);
        $this->buildPluginProperty($em, $plugin, 'key', 'base_url', $cp2);
        $this->buildPluginProperty($em, $plugin, 'definitional', 'true', $cp2);
        $em->flush();
    }

    private function buildPluginProperty(ObjectManager $em, Plugin $plugin, $key, $value = null, PluginProperty $parent = null) {
        $p = new PluginProperty();
        $p->setPlugin($plugin);
        $p->setPropertyKey($key);
        $p->setPropertyValue($value);
        $p->setParent($parent);
        $em->persist($p);
        return $p;
    }

    /**
     * {@inheritDocs}
     */
    protected function getEnvironments()
    {
        return array('test');
    }

    /**
     * Must be loaded after PLN test data.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 2;
    }

}
