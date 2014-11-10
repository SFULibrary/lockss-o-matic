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

namespace LOCKSSOMatic\PluginBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store plugin for an object, class, or global configuration in the database.
 */
class LomPluginData
{
    /**
     * @var integer
     */
    private $id;


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
     * @var string
     */
    private $domain;

    /**
     * @var integer
     */
    private $objectId;

    /**
     * @var string
     */
    private $datakey;

    /**
     * @var \stdClass
     */
    private $value;

    /**
     * Set domain
     *
     * @param string $domain
     * @return LomPluginData
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     * @return LomPluginData
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set value
     *
     * @param \stdClass $value
     * @return LomPluginData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return \stdClass 
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @var string
     */
    private $plugin;


    /**
     * Set plugin
     *
     * @param string $plugin
     * @return LomPluginData
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return string 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set datakey
     *
     * @param string $datakey
     * @return LomPluginData
     */
    public function setDatakey($datakey)
    {
        $this->datakey = $datakey;

        return $this;
    }

    /**
     * Get datakey
     *
     * @return string 
     */
    public function getDatakey()
    {
        return $this->datakey;
    }
}
