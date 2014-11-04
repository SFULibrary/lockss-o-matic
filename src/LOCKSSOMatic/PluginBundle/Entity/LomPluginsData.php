<?php

namespace LOCKSSOMatic\PluginBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LomPluginsData
 */
class LomPluginsData
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $objectId;

    /**
     * @var string
     */
    private $key;

    /**
     * @var \stdClass
     */
    private $value;

    /**
     * @var \LOCKSSOMatic\PluginBundle\Entity\LomPlugin
     */
    private $lomPlugin;


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
     * Set domain
     *
     * @param string $domain
     * @return LomPluginsData
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
     * @param \int $objectId
     * @return LomPluginsData
     */
    public function setObjectId(\int $objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return \int 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return LomPluginsData
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param \stdClass $value
     * @return LomPluginsData
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
     * Set lomPlugin
     *
     * @param \LOCKSSOMatic\PluginBundle\Entity\LomPlugin $lomPlugin
     * @return LomPluginsData
     */
    public function setLomPlugin(\LOCKSSOMatic\PluginBundle\Entity\LomPlugin $lomPlugin = null)
    {
        $this->lomPlugin = $lomPlugin;

        return $this;
    }

    /**
     * Get lomPlugin
     *
     * @return \LOCKSSOMatic\PluginBundle\Entity\LomPlugin 
     */
    public function getLomPlugin()
    {
        return $this->lomPlugin;
    }
}
