<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * General purpose exception thrown when the SWORD spec doesn't provide something
 * more specific.
 */
class BadRequestException extends ApiException
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
        $message = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
        $this->errorUri = 'http://purl.org/net/sword/error/ErrorBadRequest';
    }
}
