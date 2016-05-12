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
        $p5->setParent($p3);
        $p5->setPropertyKey('key');
        $p5->setPropertyValue('base_url');
        $manager->persist($p5);
        $this->referenceRepository->setReference('pluginproperty.5', $p5);        

        $p6 = new PluginProperty();
        $p6->setPlugin($this->referenceRepository->getReference('plugin'));
        $p6->setParent($p3);
        $p6->setPropertyKey('definitional');
        $p6->setPropertyValue('true');
        $manager->persist($p6);
        $this->referenceRepository->setReference('pluginproperty.6', $p6);        

        $p7 = new PluginProperty();
        $p7->setPlugin($this->referenceRepository->getReference('plugin'));
        $p7->setParent($p2);
        $p7->setPropertyKey('configparamdescr');
        $manager->persist($p7);
        $this->referenceRepository->setReference('pluginproperty.7', $p7);        
 
        $p8 = new PluginProperty();
        $p8->setPlugin($this->referenceRepository->getReference('plugin'));
        $p8->setParent($p7);
        $p8->setPropertyKey('key');
        $p8->setPropertyValue('year');
        $manager->persist($p8);
        $this->referenceRepository->setReference('pluginproperty.8', $p8);        

        $p9 = new PluginProperty();
        $p9->setPlugin($this->referenceRepository->getReference('plugin'));
        $p9->setParent($p7);
        $p9->setPropertyKey('definitional');
        $p9->setPropertyValue('false');
        $manager->persist($p9);
        $this->referenceRepository->setReference('pluginproperty.9', $p9);        
        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}