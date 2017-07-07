<?php

namespace LOCKSSOMatic\UserBundle\Security\Acl\Permission;

use ArrayObject;

/**
 * PLN Access levels for ACL checks.
 */
class PlnAccessLevels
{
    /**
     * Array mapping names to human-readable labels.
     *
     * @var type
     */
    private static $levels = array(
        'PLNADMIN' => 'Admin',
        'DEPOSIT' => 'Deposit',
        'MONITOR' => 'Monitor',
    );

    /**
     * Get the names of the permission levels.
     *
     * @return type
     */
    public static function names() {
        return array_keys(static::$levels);
    }

    /**
     * Get a copy of the permission levels.
     *
     * @return array
     */
    public static function levels() {
        $ao = new ArrayObject(self::$levels);

        return $ao->getArrayCopy();
    }
}
