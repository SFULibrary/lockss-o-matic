<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

/**
 * If an entity is directly or indirectly related to a PLN, it should implement this
 * interface.
 */
interface GetPlnInterface
{
    /**
     * Get the PLN for an entity.
     *
     * @return Pln[]
     */
    public function getPln();
}
