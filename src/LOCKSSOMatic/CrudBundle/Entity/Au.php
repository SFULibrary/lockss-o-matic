<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Au
 *
 * @ORM\Table(name="aus", indexes={@ORM\Index(name="IDX_2D10D530C8BA1A08", columns={"pln_id"}), @ORM\Index(name="IDX_2D10D530EC46F62F", columns={"plugin_id"}), @ORM\Index(name="IDX_2D10D530DCEFBC03", columns={"contentprovider_id"})})
 * @ORM\Entity
 */
class Au
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
     * @var boolean
     *
     * @ORM\Column(name="managed", type="boolean", nullable=false)
     */
    private $managed;

    /**
     * @var string
     *
     * @ORM\Column(name="auid", type="string", length=512, nullable=true)
     */
    private $auid;

    /**
     * @var string
     *
     * @ORM\Column(name="manifest_url", type="string", length=512, nullable=true)
     */
    private $manifestUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=512, nullable=true)
     */
    private $comment;

    /**
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="aus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    /**
     * @var ContentProvider
     *
     * @ORM\ManyToOne(targetEntity="ContentProvider", inversedBy="aus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contentprovider_id", referencedColumnName="id")
     * })
     */
    private $contentprovider;

    /**
     * @var Plugin
     *
     * @ORM\ManyToOne(targetEntity="Plugin", inversedBy="aus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     * })
     */
    private $plugin;

    /**
     * @ORM\OneToMany(targetEntity="AuProperty", mappedBy="au")
     * @var ArrayCollection
     */
    private $auProperties;

    /**
     * @ORM\OneToMany(targetEntity="AuStatus", mappedBy="au")
     * @var ArrayCollection
     */
    private $auStatus;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="au")
     * @var ArrayCollection
     */
    private $content;


    public function __construct() {
        $this->managed = false;
        $this->auProperties = new ArrayCollection();
        $this->auStatus = new ArrayCollection();
        $this->content = new ArrayCollection();
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
     * Set managed
     *
     * @param boolean $managed
     * @return Au
     */
    public function setManaged($managed)
    {
        $this->managed = $managed;

        return $this;
    }

    /**
     * Get managed
     *
     * @return boolean 
     */
    public function getManaged()
    {
        return $this->managed;
    }

    /**
     * Set auid
     *
     * @param string $auid
     * @return Au
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
     * @return Au
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
     * Set comment
     *
     * @param string $comment
     * @return Au
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set pln
     *
     * @param Pln $pln
     * @return Au
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set contentprovider
     *
     * @param ContentProvider $contentprovider
     * @return Au
     */
    public function setContentprovider(ContentProvider $contentprovider = null)
    {
        $this->contentprovider = $contentprovider;

        return $this;
    }

    /**
     * Get contentprovider
     *
     * @return ContentProvider
     */
    public function getContentprovider()
    {
        return $this->contentprovider;
    }

    /**
     * Set plugin
     *
     * @param Plugin $plugin
     * @return Au
     */
    public function setPlugin(Plugin $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Add auProperties
     *
     * @param AuProperty $auProperties
     * @return Au
     */
    public function addAuProperty(AuProperty $auProperties)
    {
        $this->auProperties[] = $auProperties;

        return $this;
    }

    /**
     * Remove auProperties
     *
     * @param AuProperty $auProperties
     */
    public function removeAuProperty(AuProperty $auProperties)
    {
        $this->auProperties->removeElement($auProperties);
    }

    /**
     * Get auProperties
     *
     * @return Collection
     */
    public function getAuProperties()
    {
        return $this->auProperties;
    }

    /**
     * Add auStatus
     *
     * @param AuStatus $auStatus
     * @return Au
     */
    public function addAuStatus(AuStatus $auStatus)
    {
        $this->auStatus[] = $auStatus;

        return $this;
    }

    /**
     * Remove auStatus
     *
     * @param AuStatus $auStatus
     */
    public function removeAuStatus(AuStatus $auStatus)
    {
        $this->auStatus->removeElement($auStatus);
    }

    /**
     * Get auStatus
     *
     * @return Collection
     */
    public function getAuStatus()
    {
        return $this->auStatus;
    }

    /**
     * Add content
     *
     * @param Content $content
     * @return Au
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
}
