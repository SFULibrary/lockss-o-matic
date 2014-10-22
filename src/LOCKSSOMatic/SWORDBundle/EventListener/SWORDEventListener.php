<?php

namespace LOCKSSOMatic\SWORDBundle\EventListener;

use LOCKSSOMatic\SWORDBundle\Controller\DefaultController as SWORDController;
use LOCKSSOMatic\SWORDBundle\Exceptions\ApiException;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

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

    public function __construct(TwigEngine $templating, Logger $logger)
    {
        $this->templating = $templating;
        $this->logger = $logger;
    }

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
        }
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->controller = $event->getController();
    }
}
