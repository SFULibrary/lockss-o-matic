<?php

namespace LOCKSSOMatic\CoreBundle\Utilities;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractDataFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface {

    protected $container;

    /**
     * {@inheritDocs}
     */
    public final function load(ObjectManager $em)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');
        if (in_array($kernel->getEnvironment(), $this->getEnvironments())) {
            $this->doLoad($em);
        } else {
            $this->container->get('logger')->notice('skipped.');
        }
    }

    /**
     * {@inheritDocs}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDocs}
     */
    public function getOrder()
    {
        return 1;
    }

    abstract protected function doLoad(ObjectManager $manager);

    abstract protected function getEnvironments();
}
