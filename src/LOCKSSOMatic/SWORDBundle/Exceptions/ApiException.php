<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiException extends HttpException
{
    
    protected $errorUri = '';
    
    public function __construct($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
        $this->headers['Content-type'] = 'text/xml';
    }
    
    public function getErrorUri()
    {
        return $this->errorUri;
    }
}
