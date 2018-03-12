<?php

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
    public function findByFilename(Deposit $deposit, $filename) {
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
