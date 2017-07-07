<?php

namespace LOCKSSOMatic\UserBundle\EventListener;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * User registration listener.
 *
 * @todo why is this here?
 */
class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    /**
     * Get the subscribed events for the listener.
     *
     * @return array
     */
    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        );
    }

    /**
     * Redirect the user to the login page. For some reason. uh?
     *
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationInitialize(GetResponseUserEvent $event) {
        $url = $this->router->generate('fos_user_security_login');
        $response = new RedirectResponse($url);

        $event->setResponse($response);
    }
}
