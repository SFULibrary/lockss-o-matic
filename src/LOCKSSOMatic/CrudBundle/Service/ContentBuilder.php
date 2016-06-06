<?php

/*
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
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
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Component\Routing\Router;

class ContentBuilder
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
     * @var Router
     */
    private $router;

    /**
     * @var AuIdGenerator
     */
    private $idGenerator;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function setAuIdGenerator(AuIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function buildProperty(Content $content, $key, $value)
    {
        $contentProperty = new ContentProperty();
        $contentProperty->setContent($content);
        $contentProperty->setPropertyKey($key);
        $contentProperty->setPropertyValue($value);
        if ($this->em !== null) {
            $this->em->persist($contentProperty);
        }

        return $contentProperty;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return Content
     */
    public function fromSimpleXML(SimpleXMLElement $xml)
    {
        $content = new Content();
        $content->setSize((string)$xml->attributes()->size);
        $content->setChecksumType((string)$xml->attributes()->checksumType);
        $content->setChecksumValue((string)$xml->attributes()->checksumValue);
        $content->setUrl(trim((string) $xml));
        $content->setRecrawl(true);
        $content->setDepositDate();
        $this->buildProperty($content, 'journalTitle', (string)$xml->attributes('pkp', true)->journalTitle);
        $this->buildProperty($content, 'publisher', (string)$xml->attributes('pkp', true)->publisher);
        $content->setTitle($xml->attributes('pkp', true)->journalTitle);
        if ($this->em !== null) {
            $this->em->persist($content);
        }

        foreach ($xml->xpath('lom:property') as $node) {
            $this->buildProperty($content, (string) $node->attributes()->name, (string) $node->attributes()->value);
        }

        return $content;
    }

    public function fromArray($record)
    {
        $content = new Content();
        $content->setSize($record['size']);
        $content->setChecksumType($record['checksum type']);
        $content->setChecksumValue($record['checksum value']);
        $content->setUrl($record['url']);
        $content->setRecrawl(true);
        if (array_key_exists('title', $record)) {
            $content->setTitle($record['title']);
        } else {
            $content->setTitle('Generated Title');
        }
        $content->setDepositDate();

        if ($this->em !== null) {
            $this->em->persist($content);
        }

        foreach (array_keys($record) as $key) {
            $this->buildProperty($content, $key, $record[$key]);
        }

        return $content;
    }
}
