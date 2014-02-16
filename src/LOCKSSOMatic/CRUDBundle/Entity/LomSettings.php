<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LomSettings
 */
class LomSettings
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $siteName;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $pathToUploads;

    /**
     * @var string
     */
    private $pathToPlnFiles;


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
     * Set siteName
     *
     * @param string $siteName
     * @return LomSettings
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string 
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Set baseUrl
     *
     * @param string $baseUrl
     * @return LomSettings
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get baseUrl
     *
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return LomSettings
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set pathToUploads
     *
     * @param string $pathToUploads
     * @return LomSettings
     */
    public function setPathToUploads($pathToUploads)
    {
        $this->pathToUploads = $pathToUploads;

        return $this;
    }

    /**
     * Get pathToUploads
     *
     * @return string 
     */
    public function getPathToUploads()
    {
        return $this->pathToUploads;
    }

    /**
     * Set pathToPlnFiles
     *
     * @param string $pathToPlnFiles
     * @return LomSettings
     */
    public function setPathToPlnFiles($pathToPlnFiles)
    {
        $this->pathToPlnFiles = $pathToPlnFiles;

        return $this;
    }

    /**
     * Get pathToPlnFiles
     *
     * @return string 
     */
    public function getPathToPlnFiles()
    {
        return $this->pathToPlnFiles;
    }
}
