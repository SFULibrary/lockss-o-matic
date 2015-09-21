<?php

namespace LOCKSSOMatic\CrudBundle\Tests\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;

class ContentOwnerTest extends AbstractTestCase {

    /**
     * @var ContentOwner
     */
    protected $owner;
    
    protected function setUp() {
        parent::setUp();
        $this->owner = $this->references->getReference('owner');
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     */
    public function testBadEmail() {
        $owner = new ContentOwner();
        $owner->setEmailAddress('not an email address');
        $this->em->persist($owner);
        $this->em->flush();
        $this->fail('No exception thrown');
    }

    public function testToString() {
        $this->assertEquals('Test Owner', "{$this->owner}");
    }

}