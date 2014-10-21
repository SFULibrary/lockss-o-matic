<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

class OnBehalfOfMissingException extends BadRequestException {
    
    public function __construct($message = '', \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $str = 'On-Behalf-Of header missing. LOCKSSOMatic requires the On-Behalf-Of HTTP header for service documents. ';        
        parent::__construct($str . $message, $previous, $headers, $code);
    }
    
}