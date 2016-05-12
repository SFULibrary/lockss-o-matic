<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\PluginProperty;

class LoadPluginProperty extends AbstractDataFixture {
    public function getOrder() {
        return 2;
    }
    
    protected function doLoad(ObjectManager $manager) {
        $p1 = new PluginProperty();
        $p1->setPlugin($this->referenceRepository->getReference('plugin'));
        $p1->setPropertyKey('plugin_identifier');
        $p1->setPropertyValue('ca.sfu.plugin.test');        
        $manager->persist($p1);
        $this->referenceRepository->setReference('pluginproperty.1', $p1);        
        
        $p2 = new PluginProperty();
        $p2->setPlugin($this->referenceRepository->getReference('plugin'));
        $p2->setPropertyKey('plugin_config_props');
        $manager->persist($p2);
        $this->referenceRepository->setReference('pluginproperty.2', $p2);        
        
        $p3 = new PluginProperty();
        $p3->setPlugin($this->referenceRepository->getReference('plugin'));
        $p3->setParent($p2);
        $p3->setPropertyKey('configparamdescr');
        $manager->persist($p3);
        $this->referenceRepository->setReference('pluginproperty.3', $p3);        
        
        $p4 = new PluginProperty();
        $p4->setPlugin($this->referenceRepository->getReference('plugin'));
        $p4->setPropertyKey('param.nop');
        $p4->setPropertyValue('not a real param');
        $manager->persist($p4);
        $this->referenceRepository->setReference('pluginproperty.4', $p4);        

        $p5 = new PluginProperty();
        $p5->setPlugin($this->referenceRepository->getReference('plugin'));
        $p5->setParent($p2);
        $p5->setPropertyKey('key');
        $p5->setPropertyValue('base_url');
        $manager->persist($p5);
        $this->referenceRepository->setReference('pluginproperty.5', $p5);        

        $p6 = new PluginProperty();
        $p6->setPlugin($this->referenceRepository->getReference('plugin'));
        $p6->setParent($p5);
        $p6->setPropertyKey('definitional');
        $p6->setPropertyValue('true');
        $manager->persist($p6);
        $this->referenceRepository->setReference('pluginproperty.6', $p6);        

        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}