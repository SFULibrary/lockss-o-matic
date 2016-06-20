<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Func;

class ContentRepository extends EntityRepository
{
    public function findByFilename(Deposit $deposit, $filename)
    {
        $config = $this->getEntityManager()->getConfiguration();
        $config->addCustomStringFunction('SUBSTRING_INDEX', 'DoctrineExtensions\Query\Mysql\SubstringIndex');
        
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.deposit = :deposit');
        $qb->setParameter('deposit', $deposit);
        $qb->andWhere("SUBSTRING_INDEX(c.url, '/', '-1') = :filename");
        $qb->setParameter('filename', $filename);
        return $qb->getQuery()->getResult();
    }
}
