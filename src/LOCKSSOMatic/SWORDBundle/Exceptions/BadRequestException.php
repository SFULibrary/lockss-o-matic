<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use LOCKSSOMatic\SWORDBundle\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends ApiException {
        
    public function __construct($message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
        $this->errorUri = 'http://purl.org/net/sword/error/ErrorBadRequest';
    }
    
}