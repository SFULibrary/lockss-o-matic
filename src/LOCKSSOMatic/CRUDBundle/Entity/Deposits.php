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

namespace LOCKSSOMatic\CRUDBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

/**
 * Deposits
 */
class Deposits
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var DateTime
     */
    private $dateDeposited;

    /**
     * @var Collection
     */
    private $content;

    /**
     * @var ContentProviders
     */
    private $contentProvider;

    /**
     * @var string
     */
    private $summary;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
    }

    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return Deposits
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Deposits
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateDeposited
     *
     * @param DateTime $dateDeposited
     * @return Deposits
     */
    public function setDateDeposited()
    {
        $this->dateDeposited = new DateTime();

        return $this;
    }

    /**
     * Get dateDeposited
     *
     * @return DateTime
     */
    public function getDateDeposited()
    {
        return $this->dateDeposited;
    }

    /**
     * Add content
     *
     * @param Content $content
     * @return Deposits
     */
    public function addContent(Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param Content $content
     */
    public function removeContent(Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return Collection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set contentProvider
     *
     * @param ContentProviders $contentProvider
     * @return Deposits
     */
    public function setContentProvider(ContentProviders $contentProvider = null)
    {
        $this->contentProvider = $contentProvider;

        return $this;
    }

    /**
     * Get contentProvider
     *
     * @return ContentProviders
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Deposits
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }
}
