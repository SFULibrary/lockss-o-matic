<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
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

namespace LOCKSSOMatic\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Override the FOSUserBundle RegistrationController - we don't want
 * public user registration.
 *
 * All actions respond with a redirect to the admin user page.
 */
class RegistrationController extends BaseController
{

    /**
     * Redirect to an admin user page.
     *
     * @param Request $request
     * @return RedirectResponse the response.
     */
    public function registerAction(Request $request)
    {
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Only site administrators can create new users.'
        );
        $url = $this->container->get('router')->generate('admin_user_new');
        return new RedirectResponse($url);
    }

    /**
     * Redirect to an admin user page.
     *
     * @param Request $request
     * @return RedirectResponse the response.
     */
    public function checkEmailAction()
    {
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Only site administrators can create new users.'
        );
        $url = $this->container->get('router')->generate('admin_user_new');
        return new RedirectResponse($url);
    }

    /**
     * Redirect to an admin user page.
     *
     * @param Request $request
     * @return RedirectResponse the response.
     */
    public function confirmAction(Request $request, $token)
    {
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Only site administrators can create new users.'
        );
        $url = $this->container->get('router')->generate('admin_user_new');
        return new RedirectResponse($url);
    }

    /**
     * Redirect to an admin user page.
     *
     * @param Request $request
     * @return RedirectResponse the response.
     */
    public function confirmedAction()
    {
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Only site administrators can create new users.'
        );
        $url = $this->container->get('router')->generate('admin_user_new');
        return new RedirectResponse($url);
    }
}
