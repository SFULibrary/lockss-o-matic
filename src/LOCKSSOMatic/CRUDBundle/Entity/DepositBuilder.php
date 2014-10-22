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

namespace LOCKSSOMatic\CRUDBundle\Entity;

use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

class DepositBuilder
{

    /**
     * 
     * @param SimpleXMLElement $xml
     * @return Deposits
     */
    public function fromSimpleXML(SimpleXMLElement $xml)
    {
        $deposit = new Deposits();
        $ns = new Namespaces();
        $id = preg_replace('/^urn:uuid:/', '', $xml->children($ns::ATOM)->id[0]);
        $title = $xml->children($ns::ATOM)->title[0];

        $deposit->setTitle((string) $title);
        $deposit->setUuid($id);

        return $deposit;
    }

}
