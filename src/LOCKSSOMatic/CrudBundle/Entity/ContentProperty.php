<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pln Properties are hierarchial.
 *
 * @ORM\Table(name="content_properties")
 * @ORM\Entity
 */
class ContentProperty
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * The name of the property.
     *
     * @var string
     *
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * The value of the property. Parent properties don't have values. The value
     * may be an array.
     *
     * @var string|array
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * True if the property value is a list/array.
     * @var boolean
     *
     * @ORM\Column(name="is_list", type="boolean", nullable=false)
     */
    private $isList;

    /**
     * The Content for the property
     * 
     * @var Content
     *
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="properties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    public function __construct() {
        $this->isList = false;
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
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return PlnProperty
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey
     *
     * @return string 
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue
     *
     * @param string|array $propertyValue
     * @return PlnProperty
     */
    public function setPropertyValue($propertyValue)
    {
        if(is_array($propertyValue)) {
            $this->isList = true;
            $this->propertyValue = serialize($propertyValue);
        } else {
            $this->isList = false;
            $this->propertyValue = $propertyValue;
        }

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return mixed
     */
    public function getPropertyValue()
    {
        if($this->isList) {
            return unserialize($this->propertyValue);
        }
        return $this->propertyValue;
    }

    /**
     * Return true if the value of the property is a list.
     *
     * @return boolean
     */
    public function isList() {
        return $this->isList;
    }


    /**
     * Set isList
     *
     * @param boolean $isList
     * @return ContentProperty
     */
    public function setIsList($isList)
    {
        $this->isList = $isList;

        return $this;
    }

    /**
     * Get isList
     *
     * @return boolean 
     */
    public function getIsList()
    {
        return $this->isList;
    }

    /**
     * Set content
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\Content $content
     * @return ContentProperty
     */
    public function setContent(\LOCKSSOMatic\CrudBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \LOCKSSOMatic\CrudBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }
}
