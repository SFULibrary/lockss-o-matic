<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

/**
 * Exception thrown when the client tries to access an unknown deposit.
 */
class DepositUnknownException extends BadRequestException
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
        $str = 'A valid deposit UUID is required. ';
        parent::__construct($str.$message, $previous, $headers, $code);
    }
}
