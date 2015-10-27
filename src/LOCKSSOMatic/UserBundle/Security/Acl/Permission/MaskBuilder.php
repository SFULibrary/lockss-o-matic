<?php

namespace LOCKSSOMatic\UserBundle\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

/**
 * Add three custom ACL masks for working with PLNs and the associated
 * boxes, aus, etc.
 *
 * @link http://stackoverflow.com/questions/15763502
 */
class MaskBuilder extends BaseMaskBuilder
{
    const MASK_PLNADMIN = 256;      // 1 << 8
    const MASK_DEPOSIT = 512;       // 1 << 9
    const MASK_MONITOR = 1024;      // 1 << 10

    const CODE_PLNADMIN = 'A';      // A for Admin.
    const CODE_DEPOSIT = 'P';       // P for dePosit
    const CODE_MONITOR = 'T';       // T for moniTor
}
