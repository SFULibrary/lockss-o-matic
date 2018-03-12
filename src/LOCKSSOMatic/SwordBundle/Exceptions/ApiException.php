<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * ApiException is the base class for all SWORD exceptions. It includes everything
 * necessary to build a SWORD error document.
 */
abstract class ApiException extends HttpException
{
    /**
     * SWORD error documents should (in the RFC2119 sense) include an error URI
     * to indicate the type of error in a machine-readable way.
     *
     * @var string
     */
    protected $errorUri = '';

    /**
     * @param int        $statusCode HTTP status code
     * @param string     $message    Exception message
     * @param \Exception $previous   previous exception, if any
     * @param array      $headers    HTTP headers to set in the response
     * @param int        $code       exception code
     */
    public function __construct(
        $statusCode,
        $message = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
        $this->headers['Content-type'] = 'text/xml';
    }

    /**
     * Get the Error Uri.
     *
     * @return string
     */
    public function getErrorUri() {
        return $this->errorUri;
    }
}
