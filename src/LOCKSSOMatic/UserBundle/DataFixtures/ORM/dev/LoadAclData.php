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

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM\dev;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder;
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
        $access = $this->getLomAccess();
        $access->grantAccess('PLNADMIN', $this->getReference('pln-dewey'), $this->getReference('dewey-admin'));
        $access->grantAccess('DEPOSIT', $this->getReference('pln-dewey'), $this->getReference('dewey-depositor'));
        $access->grantAccess('MONITOR', $this->getReference('pln-dewey'), $this->getReference('dewey-monitor'));

        $access->grantAccess('PLNADMIN', $this->getReference('pln-franklin'), $this->getReference('franklin-admin'));
        $access->grantAccess('DEPOSIT', $this->getReference('pln-franklin'), $this->getReference('franklin-depositor'));
        $access->grantAccess('MONITOR', $this->getReference('pln-franklin'), $this->getReference('franklin-monitor'));

        $access->grantAccess('PLNADMIN', $this->getReference('pln-dewey'), $this->getReference('shared-admin'));
        $access->grantAccess('DEPOSIT', $this->getReference('pln-dewey'), $this->getReference('shared-depositor'));
        $access->grantAccess('MONITOR', $this->getReference('pln-dewey'), $this->getReference('shared-monitor'));

        $access->grantAccess('PLNADMIN', $this->getReference('pln-franklin'), $this->getReference('shared-admin'));
        $access->grantAccess('DEPOSIT', $this->getReference('pln-franklin'), $this->getReference('shared-depositor'));
        $access->grantAccess('MONITOR', $this->getReference('pln-franklin'), $this->getReference('shared-monitor'));
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
        return array('dev');
    }
}
