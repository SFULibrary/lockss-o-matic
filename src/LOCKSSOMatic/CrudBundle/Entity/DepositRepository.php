<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Func;

class DepositRepository extends EntityRepository
{
    
    public function search(Pln $pln, $q) {
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
