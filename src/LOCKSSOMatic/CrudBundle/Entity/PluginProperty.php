<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hierarchial plugin property as defined by a plugin XML file.
 *
 * @ORM\Table(name="plugin_properties")
 * @ORM\Entity
 */
class PluginProperty
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Name of the property, as defined by the name attribute.
     *
     * @var string
     *
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * The value of the property, if it has one. Properties that have children
     * do not have values of their own.
     *
     * @var string
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * True if the property value is a list/array.
     *
     * @var bool
     *
     * @ORM\Column(name="is_list", type="boolean", nullable=false)
     */
    private $isList;

    /**
     * The parent of the property.
     *
     * @var PluginProperty
     *
     * @ORM\ManyToOne(targetEntity="PluginProperty", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * The plugin this property describes.
     *
     * @var Plugin
     *
     * @ORM\ManyToOne(targetEntity="Plugin", inversedBy="pluginProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plugin_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $plugin;

    /**
     * The child properties of this property.
     *
     * @ORM\OneToMany(targetEntity="PluginProperty", mappedBy="parent");
     *
     * @var PluginProperty[]
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->isList = false;
    }

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
     * Set propertyKey.
     *
     * @param string $propertyKey
     *
     * @return PluginProperty
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey.
     *
     * @return string
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue.
     *
     * @param string|array $propertyValue
     *
     * @return PlnProperty
     */
    public function setPropertyValue($propertyValue)
    {
        if (is_array($propertyValue)) {
            $this->isList = true;
            $this->propertyValue = serialize($propertyValue);
        } else {
            $this->isList = false;
            $this->propertyValue = $propertyValue;
        }

        return $this;
    }

    /**
     * Get propertyValue.
     *
     * @return mixed
     */
    public function getPropertyValue()
    {
        if ($this->isList) {
            return unserialize($this->propertyValue);
        }

        return $this->propertyValue;
    }

    /**
     * Return true if the value of the property is a list.
     *
     * @return bool
     */
    public function isList()
    {
        return $this->isList;
    }

    /**
     * Set parent.
     *
     * @param PluginProperty $parent
     *
     * @return PluginProperty
     */
    public function setParent(PluginProperty $parent = null)
    {
        $this->parent = $parent;
        if ($parent !== null) {
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * Get parent.
     *
     * @return PluginProperty
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return true if the property has a parent.
     *
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent !== null;
    }

    /**
     * Set plugin.
     *
     * @param Plugin $plugin
     *
     * @return PluginProperty
     */
    public function setPlugin(Plugin $plugin = null)
    {
        $this->plugin = $plugin;
        $plugin->addPluginProperty($this);

        return $this;
    }

    /**
     * Get plugin.
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Add children.
     *
     * @param PluginProperty $children
     *
     * @return PluginProperty
     */
    public function addChild(PluginProperty $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param PluginProperty $children
     */
    public function removeChild(PluginProperty $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children.
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Return true if the property has child properties.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }
}
