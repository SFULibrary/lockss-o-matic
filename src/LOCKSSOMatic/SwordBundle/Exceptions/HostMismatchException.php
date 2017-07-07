<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

/**
 * Exception thrown when the client attempts to deposit items on a host other
 * than the one with the LOCKSS permission statement.
 */
class HostMismatchException extends BadRequestException
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
        $str = 'Content URL does not match a corresponding LOCKSS permission '
                .'URL. One or more content URLs is either unparseable or '
                .'points to a host which is different from the content '
                .'provider\'s permission statement host. ';
        parent::__construct($str.$message, $previous, $headers, $code);
    }
}
