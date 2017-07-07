<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

/**
 * Exception thrown when the client fails to send an On-Behalf-Of header with a
 * request that requires one.
 */
class OnBehalfOfMissingException extends BadRequestException
{
    /**
     * Construct the exception.
     *
     * @param string     $message
     * @param \Exception $previous
     * @param array      $headers
     * @param int        $code
     */
    public function __construct(
        $message = '',
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        $str = 'On-Behalf-Of header missing. LOCKSSOMatic requires the On-Behalf-Of HTTP header for service documents. ';
        parent::__construct($str.$message, $previous, $headers, $code);
    }
}
