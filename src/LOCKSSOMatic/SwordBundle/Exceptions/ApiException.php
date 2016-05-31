<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
    public function getErrorUri()
    {
        return $this->errorUri;
    }
}
