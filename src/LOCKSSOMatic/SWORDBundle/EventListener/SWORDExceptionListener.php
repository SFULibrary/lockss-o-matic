<?php

namespace LOCKSSOMatic\SWORDBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use LOCKSSOMatic\SWORDBundle\Exceptions\ApiException;
use Symfony\Bundle\TwigBundle\TwigEngine;

class SWORDExceptionListener
{

    /**
     * @var TwigEngine
     */
    private $templating;

    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
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
        }
    }

}
