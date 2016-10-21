<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager by setting the entity registry (which is 
     * kinda sorta ignored).
     * 
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    /**
     * Set the property generator for the AU.
     * 
     * @param AuPropertyGenerator $propGenerator
     */
    public function setPropertyGenerator(AuPropertyGenerator $propGenerator)
    {
        $this->propGenerator = $propGenerator;
    }

    /**
     * Set the AUid generator.
     * 
     * @param AuIdGenerator $idGenerator
     */
    public function setAuIdGenerator(AuIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * Build an AU for the content item.
     *
     * @return Au
     *
     * @param Content $content
     */
    public function fromContent(Content $content)
    {
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
