<?php

namespace LOCKSSOMatic\SWORDBundle\Exceptions;

use LOCKSSOMatic\SWORDBundle\Exceptions\BadRequestException;

class MaxUploadSizeExceededException extends BadRequestException
{
    
    public function __construct($message = '', \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $str = 'A content item exceeds the size limit for the content provider.';
        parent::__construct($str . $message, $previous, $headers, $code);
    }
}
