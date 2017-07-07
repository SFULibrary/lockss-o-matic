<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Plugin repository to simplify some queries.
 */
class PluginRepository extends EntityRepository
{
    /**
     * Get a list of LOCKSS plugin IDs.
     *
     * @return string[]
     */
    public function pluginIds() {
        $query = $this->createQueryBuilder('p')->select('p.identifier')->distinct()->getQuery();

        return $query->getArrayResult();
    }

    /**
     * Find the versions of a plugin.
     *
     * @param string $pluginId
     * @return Collection|Plugin[]
     */
    public function findVersions($pluginId) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT p FROM LOCKSSOMaticCrudBundle:Plugin p WHERE p.identifier = :identifier ORDER BY p.version DESC'
        )->setParameter('identifier', $pluginId);

        return $query->getResult();
    }
}
