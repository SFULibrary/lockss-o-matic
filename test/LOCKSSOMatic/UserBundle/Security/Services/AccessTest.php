<?php

namespace LOCKSSOMatic\UserBundle\Security\Services;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * http://stackoverflow.com/questions/8455776/how-to-develop-by-faking-login-to-test-acls-in-symfony-2
 */
class AccessTest extends AbstractTestCase {

    /**
     * @var Access
     */
    protected $access;
    
    protected $container;
    
    public function setUp() {
        parent::setUp();
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->access = $this->getContainer()->get('lom.access');
    }

    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadAclData',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }

    public function testSetup() {
        $this->assertInstanceOf('LOCKSSOMatic\UserBundle\Security\Services\Access', $this->access);
    }

    public function testCheckAdminAccess() {
        $em = $this->container->get('doctrine')->getEntityManager();
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'admin@example.com'));
        
        $firewall = 'main_firewall';
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        $this->container->get('security.context')->setToken($token);

        $session = $this->container->get('session');
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();
        $this->access->setAuthChecker($this->container->get('security.authorization_checker'));
        $this->access->setTokenStorage($this->container->get('security.token_storage'));
        $this->assertTrue($this->access->hasAccess('ROLE_ADMIN'));        
        $this->assertTrue($this->access->hasAccess('ROLE_USER'));        
        $this->assertTrue($this->access->hasAccess('MONITOR'));
        $this->assertTrue($this->access->hasAccess('NON_EXISTANT_PERMISSION'));
    }

}
