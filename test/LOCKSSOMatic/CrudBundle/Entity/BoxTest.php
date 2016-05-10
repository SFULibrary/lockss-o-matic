<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class BoxTest extends AbstractTestCase
{

    /**
     * @var Box
     */
    protected $box;

    public function setUp()
    {
        parent::setUp();
        $this->box = new Box();
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBox',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }

}
