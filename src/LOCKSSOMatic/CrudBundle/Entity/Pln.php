<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Private LOCKSS Network.
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 */
class Pln
{
    /**
     * LOCKSS will only recognize these properties in an XML file if they
     * are lists.
     *
     * @todo In PHP >=5.6 this can be a const array.
     */
    private static $LIST_REQUIRED = array(
        'org.lockss.id.initialV3PeerList',
        'org.lockss.titleDbs',
        'org.lockss.plugin.registries',
    );

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Name of the PLN.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * Description of the PLN.
     *
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * The username for LOCKSSOMatic to communicate with the box. Not in the
     * lockss.xml file.
     *
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, nullable=true)
     */
    private $username;

    /**
     * The password for LOCKSSOMatic to communicate with the box.
     *
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * A list of all AUs in the PLN. Probably very large.
     *
     * @ORM\OneToMany(targetEntity="Au", mappedBy="pln")
     *
     * @var ArrayCollection|Au[]
     */
    private $aus;

    /**
     * List of boxes in the PLN.
     *
     * @ORM\OneToMany(targetEntity="Box", mappedBy="pln");
     *
     * @var ArrayCollection|Box[]
     */
    private $boxes;

    /**
     * Java Keystore file.
     *
     * @var Keystore
     * @ORM\OneToOne(targetEntity="Keystore", inversedBy="pln")
     * @ORM\JoinColumn(name="keystore_id", referencedColumnName="id")
     */
    private $keystore;

    /**
     * PLN Properties, as defined by the lockss.xml file and LOCKSSOMatic.
     *
     * @ORM\Column(name="property", type="array", nullable=true);
     *
     * @var array
     */
    private $properties;

    /**
     * List of content providers for this PLN. Each provider is associated with
     * exactly one PLN.
     *
     * @ORM\OneToMany(targetEntity="ContentProvider", mappedBy="pln")
     *
     * @var Pln[]
     */
    private $contentProviders;

    /**
     * Construct a PLN.
     */
    public function __construct() {
        $this->aus = new ArrayCollection();
        $this->boxes = new ArrayCollection();
        $this->properties = array();
        $this->contentProviders = new ArrayCollection();
        $this->plugins = new ArrayCollection(); // $this->plugins is not defined here.
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Pln
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Synonym for getName().
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * Add aus.
     *
     * @param Au $aus
     *
     * @return Pln
     */
    public function addAus(Au $aus) {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus.
     *
     * @param Au $aus
     */
    public function removeAus(Au $aus) {
        $this->aus->removeElement($aus);
    }

    /**
     * Get aus.
     *
     * @return ArrayCollection|Au[]
     */
    public function getAus() {
        return $this->aus;
    }

    /**
     * Count the AUs in this PLN.
     *
     * @return int
     */
    public function countAus() {
        return $this->aus->count();
    }

    /**
     * Add boxes.
     *
     * @param Box $boxes
     *
     * @return Pln
     */
    public function addBox(Box $boxes) {
        $this->boxes[] = $boxes;

        return $this;
    }

    /**
     * Remove box.
     *
     * @param Box $box
     */
    public function removeBox(Box $box) {
        $this->boxes->removeElement($box);
    }

    /**
     * Get boxes.
     *
     * @return ArrayCollection|Box[]
     */
    public function getBoxes() {
        return $this->boxes;
    }
    
    public function getActiveBoxes() {
        return $this->boxes->filter(function(Box $box) {
            return $box->getActive();
        });
    }

    /**
     * Set a property for this PLN.
     *
     * @param string $key
     * @param string $value
     *
     * @return Pln
     */
    public function setProperty($key, $value) {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Remove property.
     *
     * @param string $key
     *
     * @return Pln
     */
    public function deleteProperty($key) {
        unset($this->properties[$key]);

        return $this;
    }

    /**
     * Get a list of the property keys.
     *
     * @return string[]
     */
    public function getPropertyKeys() {
        return array_keys($this->properties);
    }

    /**
     * Get a property. If the property key is an array, or if it is in
     * self::$LIST_REQUIRED, or if $forceArray is true, the value returned
     * will be an array.
     *
     * @param string $key
     * @param boolean $forceArray
     *
     * @return string|array
     */
    public function getProperty($key, $forceArray = false) {
        if (!array_key_exists($key, $this->properties)) {
            return;
        }
        if (is_array($this->properties[$key])) {
            return $this->properties[$key];
        }
        if (in_array($key, self::$LIST_REQUIRED) || $forceArray) {
            return array($this->properties[$key]);
        }

        return $this->properties[$key];
    }

    /**
     * Return all of the properties. It's best to use
     * getPropertyKeys()/getProperty($key) as that will respect self::$LIST_REQUIRED.
     *
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Set all the properties in one go.
     *
     * @param type $properties
     */
    public function setProperties($properties) {
        $this->properties = $properties;
    }

    /**
     * Add contentProvider.
     *
     * @param ContentProvider $contentProvider
     *
     * @return Pln
     */
    public function addContentProvider(ContentProvider $contentProvider) {
        $this->contentProviders[] = $contentProvider;

        return $this;
    }

    /**
     * Remove contentProvider.
     *
     * @param ContentProvider $contentProvider
     */
    public function removeContentProvider(ContentProvider $contentProvider) {
        $this->contentProviders->removeElement($contentProvider);
    }

    /**
     * Get contentProviders.
     *
     * @return ArrayCollection|ContentProvider[]
     */
    public function getContentProviders() {
        return $this->contentProviders;
    }

    /**
     * Get plugins keyed by plugin identifier.
     *
     * @return Plugin[]
     */
    public function getPlugins() {
        $plugins = array();
        foreach ($this->getContentProviders() as $provider) {
            $plugin = $provider->getPlugin();
            $plugins[$plugin->getIdentifier()] = $plugin;
        }

        return $plugins;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Pln
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set keystore.
     *
     * @param Keystore $keystore
     *
     * @return Pln
     */
    public function setKeystore(Keystore $keystore = null) {
        $this->keystore = $keystore;

        return $this;
    }

    /**
     * Get keystore.
     *
     * @return Keystore
     */
    public function getKeystore() {
        return $this->keystore;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return Pln
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Pln
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
}
