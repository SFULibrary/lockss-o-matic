<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Plugins\TestCases;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class DepositTestCase extends KernelTestCase
{

    /** @var Container */
    protected $container;

    protected $providerId;

    /** @var EntityManager */
    protected $em;

    /**
     * Create a new content provider and persist it to the database. The
     * provider's uuid is stored in $providerId.
     */
    public function setUp()
    {
        $provider = new ContentProviders();
        $provider->setType('test');
        $provider->setName('Test provider 1');
        $provider->setIpAddress('127.0.0.1');
        $provider->setHostname('provider.example.com');
        $provider->setChecksumType('md5');
        $provider->setMaxFileSize('8000'); // in kB
        $provider->setMaxAuSize('10000'); // also in kB
        $provider->setPermissionUrl('http://provider.example.com/path/to/permissions');
        $this->em->persist($provider);
        $this->em->flush();
        $this->providerId = $provider->getUuid();
    }

    /**
     * Remove the content provider and all the entities it refers to.
     */
    public function tearDown()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));

        foreach($provider->getDeposits() as $deposit) {
            $this->em->refresh($deposit);
            foreach($deposit->getContent() as $content) {
                $this->em->refresh($content);
                $this->em->remove($content);
            }
            $this->em->remove($deposit);
        }
        foreach($provider->getAus() as $au) {
            $this->em->remove($au);
        }
        $this->em->remove($provider);
        $this->em->flush();
    }

    /**
     * This test requires a kernel, entity manager, and a container so create them.
     * The container is the important part, as it provides the plugin service.
     */
    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = static::$kernel->getContainer();
    }
}
