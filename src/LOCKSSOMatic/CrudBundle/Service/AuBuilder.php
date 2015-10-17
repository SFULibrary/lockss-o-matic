<?php

namespace LOCKSSOMatic\CrudBundle\Utility;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\Content;

class AuBuilder {

    /**
     * Build an AU for the content item.
     * 
     * @return Au
     * @param Content $content
     */
    public function fromContent(Content $content, ObjectManager $em = null) {
        $au = new Au();
        $au->setContentprovider($content->getDeposit()->getContentProvider());
        $au->setPln($content->getDeposit()->getContentProvider()->getPln());
        $au->setPlugin($content->getDeposit()->getContentProvider()->getPlugin());
        if($em !== null) {
            $em->persist($au);
        }

        foreach($au->getPlugin()->getDefinitionalProperties() as $property) {
            $property = new AuProperty();
            $property->setAu($au);
            $property->getPropertyKey($property);
            $property->setPropertyValue($content->getContentPropertyValue($property));
            if($em !== null) {
                $em->persist($property);
            }
        }
    }

}
