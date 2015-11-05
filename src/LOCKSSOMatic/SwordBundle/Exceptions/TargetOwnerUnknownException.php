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
     * @param string $message
     * @param \Exception $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct(
        $message = '',
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        $str = 'A valid content provider UUID is required. ';
        parent::__construct(Response::HTTP_FORBIDDEN, $str . $message, $previous, $headers, $code);
        $this->errorUri = 'http://purl.org/net/sword/error/TargetOwnerUnknown';
    }
}
