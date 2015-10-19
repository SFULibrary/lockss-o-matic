<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PluginRepository extends EntityRepository
{

    /**
     * Find a plugin based on its plugin_identifier property.
     *
     * @param string $pluginId
     * @return Plugin|null
     */
    public function findByPluginIdentifier($pluginId)
    {
        $em = $this->getEntityManager();
        $property = $em->getRepository('LOCKSSOMaticCrudBundle:PluginProperty')
            ->findOneBy(array(
                'propertyKey'   => 'plugin_identifier',
                'propertyValue' => $pluginId
            ));
        if ($property === null) {
            return null;
        }
        return $property->getPlugin();
    }

}
