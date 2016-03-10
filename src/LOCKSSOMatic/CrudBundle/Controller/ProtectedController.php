<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Protected controllers can check that the current user has the access rights
 * for an entity associated with a PLN, or the current PLN in the UI.
 */
abstract class ProtectedController extends Controller
{
    /**
     * Get the current user's selected PLN, or null if one has not been selected.
     *
     * @return Pln
     */
    protected function currentPln()
    {
        $plnId = $this->container->get('session')->get('plnId');
        if ($plnId === null) {
            return null;
        }
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
    }

    /**
     * Check that the current user has access to the PLN. Throws an exception
     * if the user does not.
     *
     * @param String $permission
     * @param Pln $pln
     */
    protected function requireAccess($permission, Pln $pln)
    {
        $access = $this->container->get('lom.access');
        $access->checkAccess($permission, $pln);
    }
}
