<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use LOCKSSOMatic\SWORDBundle\Exceptions\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class DepositUnknownException extends BadRequestException
{
    
    public function __construct($message = '', \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $str = 'A valid deposit UUID is required. ';
        parent::__construct($str . $message, $previous, $headers, $code);
    }
}
