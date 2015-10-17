<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use Monolog\Logger;

class AuBuilder {

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectManager 
     */
    private $em;
    
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }
    
    public function setRegistry(Registry $registry) {
        $this->em = $registry->getManager();
    }
    
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
