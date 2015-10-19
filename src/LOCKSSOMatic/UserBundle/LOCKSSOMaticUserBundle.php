<?php

namespace LOCKSSOMatic\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LOCKSSOMaticUserBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}
