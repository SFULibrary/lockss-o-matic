<?php


namespace LOCKSSOMatic\SwordBundle\Exceptions;

/**
 * Exception thrown when a request includes a content item which is too large
 * for the content provider.
 */
class MaxUploadSizeExceededException extends BadRequestException
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
        $str = 'A content item exceeds the size limit for the content provider.';
        parent::__construct($str.$message, $previous, $headers, $code);
    }
}
