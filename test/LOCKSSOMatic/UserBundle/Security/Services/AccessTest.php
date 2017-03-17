<?php

namespace LOCKSSOMatic\UserBundle\Security\Services;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * http://stackoverflow.com/questions/8455776/how-to-develop-by-faking-login-to-test-acls-in-symfony-2
 */
class AccessTest extends AbstractTestCase {

    /**
     * @var Access
     */
    protected $access;

    public function setUp() {
        parent::setUp();
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
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin@example.com',
            'PHP_AUTH_PW' => 'supersecret',
        ));

        $session = $this->getContainer()->get('session');
        $firewall = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_SUPER_ADMIN'));
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $this->assertTrue($this->access->checkAccess('ROLE_SUPER_ADMIN'));
    }

}
