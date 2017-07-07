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
     * Check that the current user has access to the PLN. Throws an exception
     * if the user does not.
     *
     * @param string $permission
     * @param Pln    $pln
     */
    protected function requireAccess($permission, Pln $pln) {
        $access = $this->container->get('lom.access');
        $access->checkAccess($permission, $pln);
    }

    /**
     * Find a PLN or throw an exception.
     *
     * @param int $plnId
     * @return Pln
     * @throws BadRequestException
     */
    protected function getPln($plnId) {
        $pln = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw $this->createNotFoundException('Unknown PLN.');
        }

        return $pln;
    }
}
