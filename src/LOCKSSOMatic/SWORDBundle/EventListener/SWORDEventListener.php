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

namespace LOCKSSOMatic\SWORDBundle\EventListener;

use LOCKSSOMatic\SWORDBundle\Controller\DefaultController as SWORDController;
use LOCKSSOMatic\SWORDBundle\Exceptions\ApiException;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Event listener for the SWORD bundle. This listener intercepts exceptions
 * and renders them as SWORD error documents.
 *
 * The listener only involves itself in ApiException objects, or when the
 * active controller is the SWORD controller.
 */
class SWORDEventListener
{

    /**
     * @var TwigEngine
     */
    private $templating;
    
    /**
     * @var callable
     */
    private $controller;
    
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Construct the logger. Parameters are configured in services.yml.
     *
     * @param TwigEngine $templating
     * @param Logger $logger
     */
    public function __construct(TwigEngine $templating, Logger $logger)
    {
        $this->templating = $templating;
        $this->logger = $logger;
    }

    /**
     * Catch the uncaught exceptions, and produce an error document.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof ApiException) {
            $response = new Response();
            $response->headers->add($exception->getHeaders());
            $response->headers->set('Content-Type', 'text/xml');
            $response->setStatusCode($exception->getStatusCode());
            $response->setContent($this->templating->render(
                'LOCKSSOMaticSWORDBundle:Default:exceptionDocument.xml.twig',
                array('exception' => $exception)
            ));
            $event->setResponse($response);
            return;
        }
        
        if ($this->controller[0] instanceof SWORDController) {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/xml');
            $response->setStatusCode(500);
            $response->setContent($this->templating->render(
                'LOCKSSOMaticSWORDBundle:Default:internalError.xml.twig',
                array('exception' => $exception)
            ));
            $event->setResponse($response);
            return;
        }
    }

    /**
     * Once the controller has been initialized, this event is fired. Grab
     * a reference to the active controller.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->controller = $event->getController();
    }
}
