<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A java keystore file, which LOCKSS uses to validate the plugins. Lockss.xml
 * references the keystore.
 *
 * @ORM\Table(name="keystore")
 * @ORM\Entity
 */
class Keystore
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The PLN this keystore is a part of.
     *
     * @var Pln
     *
     * @ORM\OneToOne(targetEntity="Pln", mappedBy="keystore")
     */
    private $pln;

    /**
     * Path, in the local file system, to the keystore file.
     *
     * @var string
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * Original filename, as uploaded in the web form.
     * 
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Keystore
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set pln.
     *
     * @param Pln $pln
     *
     * @return Keystore
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln.
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Keystore
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Synonym for getFilename()
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->filename;
    }
}
