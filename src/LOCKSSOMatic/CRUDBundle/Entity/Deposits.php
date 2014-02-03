<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deposits
 *
 * @ORM\Table(name="deposits", indexes={@ORM\Index(name="content_providers_id_idx", columns={"content_providers_id"}), @ORM\Index(name="uuid", columns={"uuid"})})
 * @ORM\Entity
 */
class Deposits
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
     * @var integer
     *
     * @ORM\Column(name="content_providers_id", type="integer", nullable=true)
     */
    private $contentProvidersId;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_deposited", type="datetime", nullable=true)
     */
    private $dateDeposited;



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
     * Set contentProvidersId
     *
     * @param integer $contentProvidersId
     * @return Deposits
     */
    public function setContentProvidersId($contentProvidersId)
    {
        $this->contentProvidersId = $contentProvidersId;

        return $this;
    }

    /**
     * Get contentProvidersId
     *
     * @return integer 
     */
    public function getContentProvidersId()
    {
        return $this->contentProvidersId;
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
     * @param \DateTime $dateDeposited
     * @return Deposits
     */
    public function setDateDeposited($dateDeposited)
    {
        $this->dateDeposited = $dateDeposited;

        return $this;
    }

    /**
     * Get dateDeposited
     *
     * @return \DateTime 
     */
    public function getDateDeposited()
    {
        return $this->dateDeposited;
    }
}
