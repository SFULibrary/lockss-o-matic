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
    protected function requireAccess($permission, Pln $pln)
    {
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
    protected function getPln($plnId)
    {
        $pln = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw $this->createNotFoundException('Unknown PLN.');
        }

        return $pln;
    }
}
