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

namespace LOCKSSOMatic\CrudBundle\Utility;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;
use SimpleXMLElement;

class ContentBuilder {
    
    /**
     * 
     * @param SimpleXMLElement $xml
     * @return Content
     */
    public function fromSimpleXML(SimpleXMLElement $xml, ObjectManager $em = null) {
        $content = new Content();
        $content->setSize($xml->attributes()->size);
        $content->setChecksumType($xml->attributes()->checksumType);
        $content->setChecksumValue($xml->attributes()->checksumValue);
        $content->setUrl($xml->attributes()->url);
        $content->setRecrawl(true);
        $content->setTitle('Some generated title');
        if($em !== null) {
            $em->persist($content);
        }

        foreach($xml->xpath('lom:property') as $node) {
            $contentProperty = new ContentProperty();
            $contentProperty->setContent($content);
            $contentProperty->setPropertyKey((string)$node->attributes()->name);
            $contentProperty->setPropertyValue((string)$node->attributes()->value);
            $content->addContentProperty($contentProperty);
            if($em !== null) {
                $em->persist($contentProperty);
            }
        }

        return $content;
    }
    
}