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
use Doctrine\ORM\Query\Expr\Func;

/**
 * Repository for searching deposits.
 */
class DepositRepository extends EntityRepository
{
    /**
     * Find deposits by uuid or title.
     * 
     * @param Pln $pln
     * @param string $q
     * @return ArrayCollection|Deposit[]
     */
    public function search(Pln $pln, $q)
    {
        $qb = $this->createQueryBuilder('d');
        $qb->innerJoin('d.contentProvider', 'p', 'WITH', 'p.pln = :pln');
        $qb->setParameter('pln', $pln);
        $qb->where(
            $qb->expr()->like(
                new Func('CONCAT', array('d.uuid', "' '", 'd.title')),
                "'%{$q}%'"
            )
        );

        return $qb->getQuery()->getResult();
    }
}
