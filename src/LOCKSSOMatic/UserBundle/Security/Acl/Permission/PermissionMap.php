<?php

namespace LOCKSSOMatic\UserBundle\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

/**
 * Add three custom ACL permissions for working with PLNs and the associated
 * boxes, aus, etc.
 *
 * @link http://stackoverflow.com/questions/15763502
 */
class PermissionMap extends BasicPermissionMap
{
    const PERMISSION_PLNADMIN = 'PLNADMIN';
    const PERMISSION_DEPOSIT = 'DEPOSIT';
    const PERMISSION_MONITOR = 'MONITOR';

    /**
     * Set the mapping between permissions and masks.
     */
    public function __construct() {
        parent::__construct();

        $this->map[self::PERMISSION_MONITOR] = array(
            MaskBuilder::MASK_MONITOR,
            MaskBuilder::MASK_DEPOSIT,
            MaskBuilder::MASK_PLNADMIN,
        );

        $this->map[self::PERMISSION_DEPOSIT] = array(
            MaskBuilder::MASK_DEPOSIT,
            MaskBuilder::MASK_PLNADMIN,
        );

        $this->map[self::PERMISSION_PLNADMIN] = array(
            MaskBuilder::MASK_PLNADMIN,
        );
    }
}
