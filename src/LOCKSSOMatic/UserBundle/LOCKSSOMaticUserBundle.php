<?php

namespace LOCKSSOMatic\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * User account functionality.
 */
class LOCKSSOMaticUserBundle extends Bundle
{
    /**
     * This bundle extends the FOSUserBundle.
     *
     * @return string
     */
    public function getParent() {
        return 'FOSUserBundle';
    }
}
