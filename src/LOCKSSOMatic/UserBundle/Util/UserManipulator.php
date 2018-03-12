<?php

namespace LOCKSSOMatic\UserBundle\Util;

use FOS\UserBundle\Model\UserManagerInterface;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Custom user manipulator which adds support for fullname and institution, which
 * are not part of the stock FOSUserBundle.
 *
 * http://stackoverflow.com/questions/11595261/override-symfony2-console-commands
 */
class UserManipulator
{
    /**
     * User manager.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * Construct the manipulator.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager) {
        $this->userManager = $userManager;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string $email
     * @param string $password
     * @param string $fullname
     * @param string $institution
     * @param bool   $active
     * @param bool   $superadmin
     *
     * @return User
     */
    public function create($email, $password, $fullname, $institution, $active, $superadmin) {
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setFullname($fullname);
        $user->setInstitution($institution);
        $user->setEnabled($active);
        $user->setSuperAdmin($superadmin);
        $this->userManager->updateUser($user);

        return $user;
    }
}
