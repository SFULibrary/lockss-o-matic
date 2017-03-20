<?php

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM\test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\UserBundle\Security\Services\Access;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Load ACL data fixtures.
 */
class LoadAclData extends AbstractDataFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @return Access
     */
    private function getLomAccess() {
        return $this->container->get('lom.access');
    }

    /**
     * Load the user fixtures.
     *
     * @param ObjectManager $manager
     */
    public function doload(ObjectManager $manager)
    {
//        $access = $this->getLomAccess();
//        $access->grantAccess('PLNADMIN', $this->getReference('pln-dewey'), $this->getReference('dewey-admin'));
//        $access->grantAccess('DEPOSIT', $this->getReference('pln-dewey'), $this->getReference('dewey-depositor'));
//        $access->grantAccess('MONITOR', $this->getReference('pln-dewey'), $this->getReference('dewey-monitor'));
//
//        $access->grantAccess('PLNADMIN', $this->getReference('pln-franklin'), $this->getReference('franklin-admin'));
//        $access->grantAccess('DEPOSIT', $this->getReference('pln-franklin'), $this->getReference('franklin-depositor'));
//        $access->grantAccess('MONITOR', $this->getReference('pln-franklin'), $this->getReference('franklin-monitor'));
//
//        $access->grantAccess('PLNADMIN', $this->getReference('pln-dewey'), $this->getReference('shared-admin'));
//        $access->grantAccess('DEPOSIT', $this->getReference('pln-dewey'), $this->getReference('shared-depositor'));
//        $access->grantAccess('MONITOR', $this->getReference('pln-dewey'), $this->getReference('shared-monitor'));
//
//        $access->grantAccess('PLNADMIN', $this->getReference('pln-franklin'), $this->getReference('shared-admin'));
//        $access->grantAccess('DEPOSIT', $this->getReference('pln-franklin'), $this->getReference('shared-depositor'));
//        $access->grantAccess('MONITOR', $this->getReference('pln-franklin'), $this->getReference('shared-monitor'));
    }

    /**
     * ACLs must be loaded after users and PLNs, so return a very high number.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 99;
    }

    protected function getEnvironments()
    {
        return array('test', 'dev');
    }
}
