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
 *
 * TODO: Add plugin.registries (list of URLs)
 * TODO: add plugin.titleDbs
 */
class Pln {

	private static $LIST_REQUIRED = array(
		'org.lockss.id.initialV3PeerList',
		'org.lockss.titleDbs',
		'org.lockss.plugin.registries'
	);

	/**
	 * @var integer
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
	 * Description of the PLN
	 * 
	 * @var string
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;

	/**
	 * A list of all AUs in the PLN. Probably very large.
	 *
	 * @ORM\OneToMany(targetEntity="Au", mappedBy="pln")
	 * @var ArrayCollection|Au[]
	 */
	private $aus;

	/**
	 * List of boxes in the PLN.
	 *
	 * @ORM\OneToMany(targetEntity="Box", mappedBy="pln");
	 * @var Box[]
	 */
	private $boxes;
	
	/**
	 * @var Keystore
	 * @ORM\OneToOne(targetEntity="Keystore", inversedBy="pln")
	 * @ORM\JoinColumn(name="keystore_id", referencedColumnName="id")
	 */
	private $keystore;

	/**
	 * PLN Properties, as defined by the lockss.xml file and LOCKSSOMatic.
	 *
	 * @ORM\Column(name="property", type="array", nullable=true);
	 * @var array
	 */
	private $properties;

	/**
	 * @ORM\OneToMany(targetEntity="ContentProvider", mappedBy="pln")
	 * @var Pln[]
	 */
	private $contentProviders;

	public function __construct() {
		$this->aus = new ArrayCollection();
		$this->boxes = new ArrayCollection();
		$this->properties = array();
		$this->contentProviders = new ArrayCollection();
		$this->plugins = new ArrayCollection(); // $this->plugins is not defined here.
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return Pln
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	public function __toString() {
		return $this->name;
	}

	/**
	 * Add aus
	 *
	 * @param Au $aus
	 * @return Pln
	 */
	public function addAus(Au $aus) {
		$this->aus[] = $aus;

		return $this;
	}

	/**
	 * Remove aus
	 *
	 * @param Au $aus
	 */
	public function removeAus(Au $aus) {
		$this->aus->removeElement($aus);
	}

	/**
	 * Get aus
	 *
	 * @return ArrayCollection
	 */
	public function getAus() {
		return $this->aus;
	}

	public function countAus() {
		return $this->aus->count();
	}

	/**
	 * Add boxes
	 *
	 * @param Box $boxes
	 * @return Pln
	 */
	public function addBox(Box $boxes) {
		$this->boxes[] = $boxes;

		return $this;
	}

	/**
	 * Remove boxes
	 *
	 * @param Box $boxes
	 */
	public function removeBox(Box $boxes) {
		$this->boxes->removeElement($boxes);
	}

	/**
	 * Get boxes
	 *
	 * @return ArrayCollection|Box[]
	 */
	public function getBoxes() {
		return $this->boxes;
	}

	/**
	 * Add plnProperties
	 *
	 * @param string $key
	 * @param string $value
	 * @return Pln
	 */
	public function setProperty($key, $value) {
		$this->properties[$key] = $value;
		return $this;
	}

	/**
	 * Remove property
	 *
	 * @param string $key
	 * @return Pln
	 */
	public function deleteProperty($key) {
		unset($this->properties[$key]);
		return $this;
	}

	public function getPropertyKeys() {
		return array_keys($this->properties);
	}

	/**
	 * Get plnProperties
	 * @param string $key
	 * @return string|array
	 */
	public function getProperty($key, $forceArray = false) {
		if (! array_key_exists($key, $this->properties)) {
			return null;
		}
		if(is_array($this->properties[$key])) {
			return $this->properties[$key];
		}
		if(in_array($key, self::$LIST_REQUIRED) || $forceArray) {
			return array($this->properties[$key]);
		}
		return $this->properties[$key];
	}
	
	public function getProperties() {
		return $this->properties;
	}
	
	public function setProperties($properties) {
		$this->properties = $properties;
	}

	/**
	 * Add contentProviders
	 *
	 * @param ContentProvider $contentProviders
	 * @return Pln
	 */
	public function addContentProvider(ContentProvider $contentProviders) {
		$this->contentProviders[] = $contentProviders;

		return $this;
	}

	/**
	 * Remove contentProviders
	 *
	 * @param ContentProvider $contentProviders
	 */
	public function removeContentProvider(ContentProvider $contentProviders) {
		$this->contentProviders->removeElement($contentProviders);
	}

	/**
	 * Get contentProviders
	 *
	 * @return ContentProvider[]
	 */
	public function getContentProviders() {
		return $this->contentProviders;
	}

	/**
	 * Get plugins
	 *
	 * @return Collection|Plugin[] 
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
	 * Set description
	 *
	 * @param string $description
	 * @return Pln
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string 
	 */
	public function getDescription() {
		return $this->description;
	}

    /**
     * Set keystore
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\Keystore $keystore
     * @return Pln
     */
    public function setKeystore(\LOCKSSOMatic\CrudBundle\Entity\Keystore $keystore = null)
    {
        $this->keystore = $keystore;

        return $this;
    }

    /**
     * Get keystore
     *
     * @return \LOCKSSOMatic\CrudBundle\Entity\Keystore 
     */
    public function getKeystore()
    {
        return $this->keystore;
    }
}
