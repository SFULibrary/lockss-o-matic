<?php

namespace LOCKSSOMatic\UserBundle\Security\Services;

use LOCKSSOMatic\UserBundle\Entity\User;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\PlnAccessLevels;
use Problematic\AclManagerBundle\Domain\AclManager;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
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
 *
 */
class Access
{
    private $securityContext;
    private $aclManager;
    private $aclProvider;

    /**
     * Build the access wrapper. The parameters are configured in services.yml
     *
     * @param AuthorizationChecker $securityContext
     * @param AclManager $aclManager
     * @param MutableAclProvider $aclProvider
     */
    public function __construct(AuthorizationChecker $securityContext, AclManager $aclManager, MutableAclProvider $aclProvider)
    {
        $this->securityContext = $securityContext;
        $this->aclManager = $aclManager;
        $this->aclProvider = $aclProvider;
    }

    /**
     * Check that the current user has the required permission. Throws an
     * exception if the user should not have access.
     *
     * @param string $permission
     * @param object $entity
     *
     * @return null
     *
     * @throws AccessDeniedException if access is denied.
     */
    public function checkAccess($permission, $entity = null)
    {
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
     * Check that a user (or the current user) has the required permission. Does
     * not throw an exception if permission is denied.
     *
     * @param string $permission
     * @param object $entity
     *
     * @return boolean permission granted
     */
    public function hasAccess($permission, $entity = null, $user = null)
    {
        if ($user === null) {
            $user = $this->securityContext->getToken()->getUser();
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
        try {
            $acl = $this->aclProvider->findAcl($objectId);
        } catch (\Exception $ex) {
            return false;
        }
        $securityId = UserSecurityIdentity::fromAccount($user);
        $builder = new MaskBuilder();
        $builder->add(constant('LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder::MASK_' . $permission));
        $mask = $builder->get();
        try {
            return $acl->isGranted(array($mask), array($securityId), true);
        } catch (\Exception $ex) {
            // no need to do anything here.
        }
        
        return false;
    }

    public function findAccessLevel($user, $entity) {
        $levels = PlnAccessLevels::names();
        foreach($levels as $level) {
            if($this->hasAccess($level, $entity, $user)) {
                return $level;
            }
        }
        return null;
    }

    /**
     * Grant a role to a user
     *
     * @param string $role
     * @param User $user
     */
    public function grantRole($role, $user)
    {
        $user->addRole($role);
    }
    
    /**
     * Revoke a role from a user
     *
     * @param string $role
     * @param User $user
     */
    public function revokeRole($role, $user)
    {
        $user->removeRole($role);
    }
    
    /**
     * Grant a permission to a user for an object.
     *
     * @param string $mask
     * @param object $entity
     * @param User $user
     */
    public function grantAccess($mask, $entity, $user = null)
    {
        $this->aclManager->addObjectPermission(
            $entity,
            constant('LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder::MASK_' . $mask),
            $user
        );
    }

    /**
     * Set a user's access level. Unlike grantAccess, this method first revokes
     * all object permissions for the user before adding the new one.
     *
     * @param string $mask
     * @param object $entity
     * @param User $user
     */
    public function setAccess($mask, $entity, $user = null)
    {
        $this->aclManager->revokeAllObjectPermissions($entity, $user);
        if ($mask) {
            $this->aclManager->setObjectPermission(
                $entity,
                constant('LOCKSSOMatic\UserBundle\Security\Acl\Permission\MaskBuilder::MASK_' . $mask),
                $user
            );
        }
    }

    /**
     * Revoke all access for an entity from a user.
     *
     * @param object $entity
     * @param User $user
     */
    public function revokeAccess($entity, $user = null)
    {
        $this->aclManager->revokeAllObjectPermissions(
            $entity,
            $user
        );
    }
}
