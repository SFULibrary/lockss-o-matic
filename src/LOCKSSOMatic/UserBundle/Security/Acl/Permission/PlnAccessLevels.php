<?php

namespace LOCKSSOMatic\UserBundle\Security\Acl\Permission;

use ArrayObject;

class PlnAccessLevels {

    private static $levels = array(
        'PLNADMIN' => 'Admin',
        'DEPOSIT' => 'Deposit',
        'MONITOR' => 'Monitor',
    );

    public static function names() {
        return array_keys(static::$levels);
    }

    public static function levels() {
        $ao = new ArrayObject(self::$levels);
        return $ao->getArrayCopy();
    }

}