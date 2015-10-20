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

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    protected function buildProperty(Content $content, $key, $value)
    {
        $contentProperty = new ContentProperty();
        $contentProperty->setContent($content);
        $contentProperty->setPropertyKey($key);
        $contentProperty->setPropertyValue($value);
        if($this->em !== null) {
            $this->em->persist($contentProperty);
        }
        return $contentProperty;
    }

    /**
     * 
     * @param SimpleXMLElement $xml
     * @return Content
     */
    public function fromSimpleXML(SimpleXMLElement $xml)
    {
        $content = new Content();
        $content->setSize($xml->attributes()->size);
        $content->setChecksumType($xml->attributes()->checksumType);
        $content->setChecksumValue($xml->attributes()->checksumValue);
        $content->setUrl(trim((string) $xml));
        $content->setRecrawl(true);
        $content->setTitle('Some generated title');
        if ($this->em !== null) {
            $this->em->persist($content);
        }

        foreach ($xml->xpath('lom:property') as $node) {
            $this->buildProperty($content, (string) $node->attributes()->name, (string) $node->attributes()->value);
        }

        return $content;
    }

    private function fieldOrNull($row, $headerIdx, $field) {
        if( ! array_key_exists($field, $headerIdx)) {
            return null;
        }
        $index = $headerIdx[$field];
        if( ! array_key_exists($index, $row)) {
            return null;
        }
        return $row[$index];
    }

    public function fromArray($row, $headerIdx)
    {
        $content = new Content();
        $content->setSize($this->fieldOrNull($row, $headerIdx, 'size'));
        $content->setChecksumType($this->fieldOrNull($row, $headerIdx, 'checksum type'));
        $content->setChecksumValue($this->fieldOrNull($row, $headerIdx, 'checksum value'));
        $content->setUrl($row[$headerIdx['url']]);
        $content->setRecrawl(true);
        $content->setTitle("Generated Title");
        if ($this->em !== null) {
            $this->em->persist($content);
        }
        
        foreach (array_keys($headerIdx) as $key) {
            $this->buildProperty($content, $key, $this->fieldOrNull($row, $headerIdx, $key));
        }
        return $content;
    }

}
