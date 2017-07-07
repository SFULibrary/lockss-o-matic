<?php


namespace LOCKSSOMatic\SwordBundle\Listener;

use LOCKSSOMatic\SwordBundle\Controller\SwordController;
use LOCKSSOMatic\SwordBundle\Exceptions\ApiException;
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
class SwordErrorListener
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
     * @param Logger     $logger
     */
    public function __construct(TwigEngine $templating, Logger $logger) {
        $this->templating = $templating;
        $this->logger = $logger;
    }

    /**
     * Catch the uncaught exceptions, and produce an error document.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();

        if (!$this->controller[0] instanceof SwordController) {
            return;
        }

        $this->logger->critical($exception);

        if ($exception instanceof ApiException) {
            $response = new Response();
            $response->headers->add($exception->getHeaders());
            $response->headers->set('Content-Type', 'text/xml');
            $response->setStatusCode($exception->getStatusCode());
            $response->setContent($this->templating->render(
                'LOCKSSOMaticSwordBundle:Sword:exceptionDocument.xml.twig',
                array(
                    'errorUri' => $exception->getErrorUri(),
                    'statusCode' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                )
            ));
            $event->setResponse($response);

            return;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(500);
        $response->setContent($this->templating->render(
            'LOCKSSOMaticSwordBundle:Sword:exceptionDocument.xml.twig',
            array(
                'errorUri' => '',
                'statusCode' => 500,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            )
        ));
        $event->setResponse($response);
    }

    /**
     * Once the controller has been initialized, this event is fired. Grab
     * a reference to the active controller.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event) {
        $this->controller = $event->getController();
    }
}
