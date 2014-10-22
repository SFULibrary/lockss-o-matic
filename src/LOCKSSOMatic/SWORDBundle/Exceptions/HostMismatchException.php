<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use LOCKSSOMatic\SWORDBundle\Exceptions\BadRequestException;

class HostMismatchException extends BadRequestException
{
    
    public function __construct($message = '', \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $str =  'Content URL does not match a corresponding LOCKSS permission '
                . 'URL. One or more content URLs is either unparseable or '
                . 'points to a host which is different from the content '
                . 'provider\'s permission statement host.';
        parent::__construct($str . $message, $previous, $headers, $code);
    }
}
