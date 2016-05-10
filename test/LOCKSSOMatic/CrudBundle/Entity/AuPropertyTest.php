<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuPropertyTest extends AbstractTestCase {
    
    public function testSetParent() {
        $parent = new AuProperty();
        $child = new AuProperty();

        $child->setParent($parent);
        $this->assertCount(1, $parent->getChildren());
    }
    
    public function testHasParentTrue() {
        $parent = new AuProperty();
        $child = new AuProperty();

        $child->setParent($parent);
        $this->assertTrue($child->hasParent());
    }
    
    public function testHasParentFalse() {
        $child = new AuProperty();
        $this->assertFalse($child->hasParent());
    }
    
}