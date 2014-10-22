<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
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

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpKernel\Tests\Controller;

/**
 * BoxStatus controller.
 *
 */
class BoxStatusController extends Controller
{

    /**
     * Lists all BoxStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a BoxStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
