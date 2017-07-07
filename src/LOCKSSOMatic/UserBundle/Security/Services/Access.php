<?php

namespace LOCKSSOMatic\UserBundle\Security\Services;

use FOS\UserBundle\Doctrine\UserManager;
use LOCKSSOMatic\UserBundle\Entity\User;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\PermissionMap;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\PlnAccessLevels;
use Monolog\Logger;
use Problematic\AclManagerBundle\Domain\AclManager;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wrapper around ProblematicAclBundle and around some of Symfony's ACL and role
 * features.
 *
 * Access is configured as a service (LOCKSSOMaticUserBundle::services.yml), to make managing
 * permissions in controllers easier, and as a global variable for twig templates
 * (app/config/config.yml) to make enforcing permissions a little bit easier.
 *
 * In templates: {% if lom_access.hasAccess('PLNADMIN', pln) %} Yes {% else %} No {% endif %}
 *
 * In controllers: ($this->get('lom.access')->checkAccess("PLNADMIN", $pln);
 */
class Access
{
    /**
     * @var AuthorizationChecker
     */
    private $securityContext;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var MutableAclProvider
     */
    private $aclProvider;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * Set tehe security context by way of the authorization checker.
     *
     * @param AuthorizationChecker $securityContext
     */
    public function setAuthChecker(AuthorizationChecker $securityContext) {
        $this->securityContext = $securityContext;
    }

    /**
     * Set the token storage, which contains the user token.
     *
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Set the ACL manager.
     *
     * @param AclManager $aclManager
     */
    public function setAclManager(AclManager $aclManager) {
        $this->aclManager = $aclManager;
    }

    /**
     * SEt the ACL provider.
     *
     * @param MutableAclProvider $aclProvider
     */
    public function setAclProvider(MutableAclProvider $aclProvider) {
        $this->aclProvider = $aclProvider;
    }

    /**
     * Set the logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the user manager.
     *
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager) {
        $this->userManager = $userManager;
    }

    /**
     * Check that the current user has the required permission. Throws an
     * exception if the user should not have access.
     *
     * @param string $permission
     * @param object $entity
     *
     * @throws AccessDeniedException if access is denied.
     */
    public function checkAccess($permission, $entity = null) {
        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (($entity === null) && ($this->securityContext->isGranted($permission))) {
            return;
        }

        if ($this->securityContext->isGranted($permission, $entity)) {
            return;
        }

        throw new AccessDeniedException("$permission is required for this page.");
    }

    /**
     * Get the ACL for an object ID.
     *
     * @param mixed $objectId
     * @return SplObjectStorage
     */
    protected function getAcl($objectId) {
        try {
            return $this->aclProvider->findAcl($objectId);
        } catch (AclNotFoundException $ex) {
            $this->logger->info("No ACL for {$objectId} ".$ex->getMessage());

            return $this->aclProvider->createAcl($objectId);
        }
    }

    /**
     * Check that a user (or the current user) has the required permission. Does
     * not throw an exception if permission is denied.
     *
     * @param string $permission
     * @param object $entity
     * @param User $user
     *
     * @return bool permission granted
     */
    public function hasAccess($permission, $entity = null, User $user = null) {
        if ($user === null) {
            if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $user   = $this->tokenStorage->getToken()->getUser();
            } else {
                $user = new User();
            }
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($entity === null) {
            return $user->hasRole($permission);
        }

        // because it can be any arbitrary user, not just the current user,
        // isGranted() won't work here.
        $objectId = ObjectIdentity::fromDomainObject($entity);

        $acl = $this->getAcl($objectId);
        $pm = new PermissionMap();
        $masks = $pm->getMasks($permission, $entity);

        $securityId = UserSecurityIdentity::fromAccount($user);
        try {
            return $acl->isGranted($masks, array($securityId), true);
        } catch (\Exception $ex) { // @codingStandardsIgnoreLine
            // no need to do anything here.
        }

        return false;
    }

    /**
     * Find the access level for a user in an entity.
     *
     * @param User $user
     * @param mixed $entity
     * @return string|null
     */
    public function findAccessLevel($user, $entity) {
        $levels = PlnAccessLevels::names();
        foreach ($levels as $level) {
            if ($this->hasAccess($level, $entity, $user)) {
                return $level;
            }
        }

        return null;
    }

    /**
     * Grant a role to a user.
     *
     * @param string $role
     * @param User   $user
     */
    public function grantRole($role, $user) {
        $user->addRole($role);
    }

    /**
     * Revoke a role from a user.
     *
     * @param string $role
     * @param User   $user
     */
    public function revokeRole($role, $user) {
        $user->removeRole($role);
    }

    /**
     * Grant a permission to a user for an object.
     *
     * @param string $permission
     * @param object $entity
     * @param User   $user
     */
    public function grantAccess($permission, $entity, $user = null) {
        $this->aclManager->addObjectPermission(
            $entity,
            constant('LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder::MASK_'.$permission),
            $user
        );
    }

    /**
     * Set a user's access level. Unlike grantAccess, this method first revokes
     * all object permissions for the user before adding the new one.
     *
     * @param string $permission
     * @param object $entity
     * @param User   $user
     */
    public function setAccess($permission, $entity, $user = null) {
        $this->aclManager->revokeAllObjectPermissions($entity, $user);
        if ($permission) {
            $this->grantAccess($permission, $entity, $user);
        }
    }

    /**
     * Revoke all access for an entity from a user.
     *
     * @param object $entity
     * @param User   $user
     */
    public function revokeAccess($entity, $user = null) {
        $this->aclManager->revokeAllObjectPermissions(
            $entity,
            $user
        );
    }
}
