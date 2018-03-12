<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when the client attemps to access an unknown content
 * provider.
 */
class TargetOwnerUnknownException extends ApiException
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
        $str = 'A valid content provider UUID is required. ';
        parent::__construct(Response::HTTP_FORBIDDEN, $str.$message, $previous, $headers, $code);
        $this->errorUri = 'http://purl.org/net/sword/error/TargetOwnerUnknown';
    }
}
