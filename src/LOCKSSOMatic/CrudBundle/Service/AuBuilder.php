<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use Monolog\Logger;

/**
 * Build an AU.
 */
class AuBuilder
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var AuPropertyGenerator
     */
    private $propGenerator;

    /**
     * @var AuIdGenerator
     */
    private $idGenerator;

    /**
     * Set the logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager by setting the entity registry (which is
     * kinda sorta ignored).
     *
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry) {
        $this->em = $registry->getManager();
    }

    /**
     * Set the property generator for the AU.
     *
     * @param AuPropertyGenerator $propGenerator
     */
    public function setPropertyGenerator(AuPropertyGenerator $propGenerator) {
        $this->propGenerator = $propGenerator;
    }

    /**
     * Set the AUid generator.
     *
     * @param AuIdGenerator $idGenerator
     */
    public function setAuIdGenerator(AuIdGenerator $idGenerator) {
        $this->idGenerator = $idGenerator;
    }

    /**
     * Build an AU for the content item.
     *
     * @return Au
     *
     * @param Content $content
     */
    public function fromContent(Content $content) {
        $au = new Au();
        $au->addContent($content);
        $au->setAuid($this->idGenerator->fromContent($content, false));
        $provider = $content->getDeposit()->getContentProvider();

        $au->setContentprovider($provider);
        $au->setPln($provider->getPln());
        $au->setPlugin($provider->getPlugin());

        $this->em->persist($au);
        $this->em->flush();

        $this->propGenerator->generateProperties($au);

        return $au;
    }
}
