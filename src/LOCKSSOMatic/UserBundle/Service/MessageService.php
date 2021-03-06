<?php

namespace LOCKSSOMatic\UserBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Entity\Message;
use LOCKSSOMatic\UserBundle\Entity\User;
use Monolog\Logger;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Provide some basic messaging.
 */
class MessageService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Set the logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager by way of the Doctrine registry.
     *
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry) {
        $this->em = $registry->getManager();
    }

    /**
     * Set the token storage for the current user.
     *
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Send $content to $user.
     *
     * @param string $content
     * @param User $user
     */
    public function send($content, User $user = null) {
        $message = new Message();
        $message->setContent($content);
        if ($user === null) {
            $message->setUser($this->tokenStorage->getToken()->getUser());
        } else {
            $message->setUser($user);
        }
        $this->em->persist($message);
        $this->em->flush($message);
    }
}
