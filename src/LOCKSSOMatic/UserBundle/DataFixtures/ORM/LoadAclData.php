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

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Load ACL data fixtures.
 */
class LoadAclData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Load the user fixtures.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $aclManager = $this->container->get('problematic.acl_manager');
        
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_PLNADMIN,
            $this->getReference('dewey-admin')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_DEPOSIT,
            $this->getReference('dewey-depositor')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_MONITOR,
            $this->getReference('dewey-monitor')
        );
        
        $aclManager->addObjectPermission(
            $this->getReference('pln-franklin'),
            MaskBuilder::MASK_PLNADMIN,
            $this->getReference('franklin-admin')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-franklin'),
            MaskBuilder::MASK_DEPOSIT,
            $this->getReference('franklin-depositor')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-franklin'),
            MaskBuilder::MASK_MONITOR,
            $this->getReference('franklin-monitor')
        );
        
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_PLNADMIN,
            $this->getReference('shared-admin')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_DEPOSIT,
            $this->getReference('shared-depositor')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-dewey'),
            MaskBuilder::MASK_MONITOR,
            $this->getReference('shared-monitor')
        );
        
        $aclManager->addObjectPermission(
            $this->getReference('pln-larkin'),
            MaskBuilder::MASK_PLNADMIN,
            $this->getReference('shared-admin')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-larkin'),
            MaskBuilder::MASK_DEPOSIT,
            $this->getReference('shared-depositor')
        );
        $aclManager->addObjectPermission(
            $this->getReference('pln-larkin'),
            MaskBuilder::MASK_MONITOR,
            $this->getReference('shared-monitor')
        );
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
}
