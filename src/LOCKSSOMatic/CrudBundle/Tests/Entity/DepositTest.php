<?php

namespace LOCKSSOMatic\CrudBundle\Tests\Entity;

use LOCKSSOMatic\CrudBundle\Entity\Deposit;

class DepositTest extends AbstractEntityTestCase {

    /**
     * @var Deposit
     */
    protected $deposit;
    
    protected function setUp() {
        parent::setUp();
        $this->deposit = $this->references->getReference('deposit');
    }

    public function testUuidUpperCase() {
        $this->assertEquals('1BFB4F67-D822-489A-94E6-A069D9D40A18', $this->deposit->getUuid());
    }

}