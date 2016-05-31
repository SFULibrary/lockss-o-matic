<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PluginRepository extends EntityRepository
{
    public function pluginIds()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.identifier')
            ->distinct()
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findVersions($pluginId)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT p FROM LOCKSSOMaticCrudBundle:Plugin p '
            .'WHERE p.identifier = :identifier ORDER BY p.version DESC'
        )
            ->setParameter('identifier', $pluginId);

        return $query->getResult();
    }
}
