<?php

namespace LOCKSSOMatic\UserBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Wrapper around the user repository for use in twig templates. Lets you get a
 * list of users in any template. Configure this class as a service in
 * services.yml and then call it like this: {% for user in userList() %} ... {% endfor %}.
 */
class UsersExtension extends \Twig_Extension
{
    /**
     * @var Doctrine
     */
    private $doctrine;

    /**
     * Construct the extension.
     *
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine = null) {
        $this->doctrine = $doctrine;
    }

    /**
     * Get a list of functions, keyed by function name.
     *
     * @return array
     */
    public function getFunctions() {
        return array(
            'userList' => new \Twig_SimpleFunction('userList', array($this, 'userList')),
        );
    }

    /**
     * Get a list of users.
     *
     * @return User[]
     */
    public function userList() {
        $em = $this->doctrine->getManager();
        $users = $em->getRepository('LOCKSSOMaticUserBundle:User')->findAll();

        return $users;
    }

    /**
     * Get the extension's name.
     *
     * @return string the name as configured in services.yml
     */
    public function getName() {
        return 'lom_usersextension';
    }
}
