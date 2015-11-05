<?php

namespace LOCKSSOMatic\CrudBundle\Tests\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;

class ContentProviderTest extends AbstractTestCase
{

    /**
     * @var ContentProvider
     */
    protected $provider;
    protected $references;
    protected $em;
    
    protected function setUp()
    {
        parent::setUp();
        $this->provider = $this->references->getReference('provider');
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     */
    public function testBadUuid()
    {
        $provider = new ContentProvider();
        $provider->setUuid('I am a bad UUID');
        $this->em->persist($provider);
        $this->em->flush();
    }

    public function testUuidUpperCase()
    {
        $this->assertEquals('473A1B0D-425F-417B-94CF-28C3FC04B0E2', $this->provider->getUuid());
    }

    public function testGetPermissionHost()
    {
        $this->assertEquals('example.com', $this->provider->getPermissionHost());
    }

    public function testGenerateUuid()
    {
        $this->provider->generateUuid();
        $this->assertEquals('473A1B0D-425F-417B-94CF-28C3FC04B0E2', $this->provider->getUuid());
        // doesn't change if there's already one.
    }

    public function testToString()
    {
        $this->assertEquals('Test provider', "{$this->provider}");
    }
}
