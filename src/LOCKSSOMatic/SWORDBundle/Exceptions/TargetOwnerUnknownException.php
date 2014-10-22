<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use LOCKSSOMatic\SWORDBundle\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response;

class TargetOwnerUnknownException extends ApiException
{
    
    public function __construct($message = '', \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $str = 'A valid content provider UUID is required. ';
        parent::__construct(Response::HTTP_FORBIDDEN, $str . $message, $previous, $headers, $code);
        $this->errorUri = 'http://purl.org/net/sword/error/TargetOwnerUnknown';
    }
}
