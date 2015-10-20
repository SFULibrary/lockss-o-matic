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

    protected function buildProperty(Au $au, $key, $value = null, AuProperty $parent = null) {
        $property = new AuProperty();
        $property->setAu($au);
        $property->setPropertyKey($key);
        $property->setPropertyValue($value);
        $property->setParent($parent);
        $this->em->persist($property);
        return $property;
    }
    
    /**
     * Build an AU for the content item.
     * 
     * @return Au
     * @param Content $content
     */
    public function fromContent(Content $content) {
        $au = new Au();
        $au->setContentprovider($content->getDeposit()->getContentProvider());
        $au->setPln($content->getDeposit()->getContentProvider()->getPln());
        $au->setPlugin($content->getDeposit()->getContentProvider()->getPlugin());

        $root = $this->buildProperty($au, 'lockssomatic' . uniqid('', true));
        $this->buildProperty($au, 'journalTitle', $content->getContentPropertyValue('journaltitle'), $root);
        $this->buildProperty($au, 'title', 'LOCKSSOMatic AU ' . $content->getTitle() . ' ' . $content->getDeposit()->getTitle(), $root);
        $this->buildProperty($au, 'plugin', $au->getPlugin()->getPluginIdentifier());

        foreach($au->getPlugin()->getDefinitionalProperties() as $index => $property) {
            $grouping = $this->buildProperty($au, 'param.' . $index, null, $root);
            $this->buildProperty($au, 'key', $property, $grouping);
            $this->buildProperty($au, 'value', $content->getContentPropertyValue($property), $grouping);
        }
        $this->em->persist($au);
        $this->em->flush($au);
    }

}
