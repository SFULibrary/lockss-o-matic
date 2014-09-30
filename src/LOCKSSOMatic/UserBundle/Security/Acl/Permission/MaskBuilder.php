<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
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

namespace LOCKSSOMatic\UserBundle\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

/**
 * Add three custom ACL masks for working with PLNs and the associated
 * boxes, aus, etc.
 *
 * @link http://stackoverflow.com/questions/15763502
 */
class MaskBuilder extends BaseMaskBuilder
{
    const MASK_PLNADMIN = 256;      // 1 << 8
    const MASK_DEPOSIT = 512;       // 1 << 9
    const MASK_MONITOR = 1024;      // 1 << 10
    
    const CODE_PLNADMIN = 'A';      // A for Admin.
    const CODE_DEPOSIT = 'P';       // P for dePosit
    const CODE_MONITOR = 'T';       // T for moniTor
}
