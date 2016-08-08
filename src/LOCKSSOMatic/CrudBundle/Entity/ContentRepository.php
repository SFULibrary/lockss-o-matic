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

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Searching for content by filename. 
 * 
 * @todo would finding content by URL be useful?
 */
class ContentRepository extends EntityRepository
{
    /**
     * Find a content item by filename.
     * 
     * @param Deposit $deposit
     * @param string $filename
     * @return ArrayCollection|Deposit[]
     */
    public function findByFilename(Deposit $deposit, $filename)
    {
        $content = $deposit->getContent();
        $result = array();
        foreach($content as $c) {
            if(substr_compare($c->getUrl(), $filename, -strlen($filename)) === 0) {
                $result[] = $c;
            }
        }
        return $result;
        
//        $config = $this->getEntityManager()->getConfiguration();
//        $config->addCustomStringFunction(
//            'SUBSTRING_INDEX',
//            'DoctrineExtensions\Query\Mysql\SubstringIndex'
//        );
//        
//        $qb = $this->createQueryBuilder('c');
//        $qb->where('c.deposit = :deposit');
//        $qb->setParameter('deposit', $deposit);
//        $qb->andWhere("SUBSTRING_INDEX(c.url, '/', '-1') = :filename");
//        $qb->setParameter('filename', $filename);
//        return $qb->getQuery()->getResult();
    }
}
