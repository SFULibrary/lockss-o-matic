<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Aus
 *
 * @ORM\Table(name="aus", indexes={@ORM\Index(name="plns_id_idx", columns={"plns_id"})})
 * @ORM\Entity
 */
class Aus
{
    /**
     * Property required for one-to-many relationship with Content.
     * 
     * @OneToMany(targetEntity="Content", mappedBy="content")
     */
    protected $content;

    /**
     * Property required for one-to-many relationship with AuStatus.
     * 
     * @OneToMany(targetEntity="AuStatus", mappedBy="auStatus")
     */
    protected $auStatus;

    /**
     * Property required for one-to-many relationship with AuProperties.
     * 
     * @OneToMany(targetEntity="AuProperties", mappedBy="auProperties")
     */
    protected $auProperties;

    /**
     * Initializes the $content, $auStatus, $auProperties, and $pluginProperties
     * properties.
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
        $this->auStatus = new ArrayCollection();
        $this->auProperties = new ArrayCollection();
    }

    /**
    * Property required for many-to-one relationship with Plns.
    * 
    * @ManyToOne(targetEntity="Plns", mappedBy="aus")
    * @JoinColumn(name="plns_id", referencedColumnName="id")
    */
    protected $pln;
    
    /**
    * Property required for many-to-one relationship with Plugins.
    * 
    * @ManyToOne(targetEntity="Plugins", mappedBy="aus")
    * @JoinColumn(name="plugins_id", referencedColumnName="id")
    */
    protected $plugin;    

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="plns_id", type="integer", nullable=true)
     */
    private $plnsId;

    /**
     * @var integer
     *
     * @ORM\Column(name="exteranl_title_dbs_id", type="integer", nullable=true)
     */
    private $externalTitleDbsId;

    /**
     * @var string
     *
     * @ORM\Column(name="auid", type="text", nullable=true)
     */
    private $auid;

    /**
     * @var string
     *
     * @ORM\Column(name="manifest_url", type="text", nullable=true)
     */
    private $manifestUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="open", type="integer", nullable=true)
     */
    private $open;    

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
     * Set plnsId
     *
     * @param integer $plnsId
     * @return Aus
     */
    public function setPlnsId($plnsId)
    {
        $this->plnsId = $plnsId;

        return $this;
    }

    /**
     * Get plnsId
     *
     * @return integer 
     */
    public function getPlnsId()
    {
        return $this->plnsId;
    }

    /**
     * Get externalTitleDbsId
     *
     * @return integer 
     */
    public function getExternalTitleDbsId()
    {
        return $this->externalTitleDbsId;
    }
    
    /**
     * Set externalTitleDbsId
     *
     * @param integer $exteralTitleDbsId
     * @return Aus
     */
    public function setExternalTitleDbsId($externalTitleDbsId)
    {
        $this->plnsId = $plnsId;

        return $this;
    }

    /**
     * Set auid
     *
     * @param string $auid
     * @return Aus
     */
    public function setAuid($auid)
    {
        $this->auid = $auid;

        return $this;
    }

    /**
     * Get auid
     *
     * @return string 
     */
    public function getAuid()
    {
        return $this->auid;
    }

    /**
     * Set manifestUrl
     *
     * @param string $manifestUrl
     * @return Aus
     */
    public function setManifestUrl($manifestUrl)
    {
        $this->manifestUrl = $manifestUrl;

        return $this;
    }

    /**
     * Get manifestUrl
     *
     * @return string 
     */
    public function getManifestUrl()
    {
        return $this->manifestUrl;
    }

    /**
     * Add content
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     * @return Aus
     */
    public function addContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     */
    public function removeContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add auStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus
     * @return Aus
     */
    public function addAuStatus(\LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus)
    {
        $this->auStatus[] = $auStatus;

        return $this;
    }

    /**
     * Remove auStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus
     */
    public function removeAuStatus(\LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus)
    {
        $this->auStatus->removeElement($auStatus);
    }

    /**
     * Get auStatus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuStatus()
    {
        return $this->auStatus;
    }

    /**
     * Add auProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties
     * @return Aus
     */
    public function addAuProperty(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties)
    {
        $this->auProperties[] = $auProperties;

        return $this;
    }

    /**
     * Remove auProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties
     */
    public function removeAuProperty(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties)
    {
        $this->auProperties->removeElement($auProperties);
    }

    /**
     * Get auProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuProperties()
    {
        return $this->auProperties;
    }

    /**
     * Add pluginProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties
     * @return Aus
     */
    public function addPluginProperty(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties)
    {
        $this->pluginProperties[] = $pluginProperties;

        return $this;
    }

    /**
     * Remove pluginProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties
     */
    public function removePluginProperty(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties)
    {
        $this->pluginProperties->removeElement($pluginProperties);
    }

    /**
     * Get pluginProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPluginProperties()
    {
        return $this->pluginProperties;
    }

    /**
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return Aus
     */
    public function setPln(\LOCKSSOMatic\CRUDBundle\Entity\Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plns 
     */
    public function getPln()
    {
        return $this->pln;
    }
    /**
     * @var integer
     */
    private $pluginsId;


    /**
     * Set pluginsId
     *
     * @param integer $pluginsId
     * @return Aus
     */
    public function setPluginsId($pluginsId)
    {
        $this->pluginsId = $pluginsId;

        return $this;
    }

    /**
     * Get pluginsId
     *
     * @return integer 
     */
    public function getPluginsId()
    {
        return $this->pluginsId;
    }

    /**
     * Set plugin
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plugins $plugin
     * @return Aus
     */
    public function setPlugin(\LOCKSSOMatic\CRUDBundle\Entity\Plugins $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plugins 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set open
     *
     * @param integer $open
     * @return Aus
     */
    public function setOpen($open)
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Get open
     *
     * @return integer 
     */
    public function getOpen()
    {
        return $this->open;
    }
}
