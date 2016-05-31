<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;

class LoadContentProperty extends AbstractDataFixture {
    public function getOrder() {
        return 5;
    }
    
    protected function buildProperty(ObjectManager $manager, Content $content, $key, $value, $name = null) {
        $p = new ContentProperty();
        $p->setContent($content);
        $p->setPropertyKey($key);
        $p->setPropertyValue($value);
        if($name !== null) {
            $this->referenceRepository->setReference($name, $p);
        }
        $manager->persist($p);
    }
    
    protected function doLoad(ObjectManager $manager) {
        $c1 = $this->referenceRepository->getReference('content.1');
        $this->buildProperty($manager, $c1, 'base_url', 'http://example.com/path/htap');
        $this->buildProperty($manager, $c1, 'lockss_only', 'pdq');
        $this->buildProperty($manager, $c1, 'sillyprop', array('foo', 'bar/'));
        
        $c2 = $this->referenceRepository->getReference('content.2');
        $this->buildProperty($manager, $c2, 'base_url', 'http://example.com/path/htap');
        $this->buildProperty($manager, $c2, 'lockss_only', 'pdq');

        $c3 = $this->referenceRepository->getReference('content.3');
        $this->buildProperty($manager, $c3, 'base_url', 'http://example.com/path/htap');
        $this->buildProperty($manager, $c3, 'lockss_only', 'pdq');

        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}