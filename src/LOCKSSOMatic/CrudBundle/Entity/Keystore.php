<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Keystore
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Keystore
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The PLN this box is a part of.
     *
     * @var Pln
     *
     * @ORM\OneToOne(targetEntity="Pln", inversedBy="keystore")
     */
    private $pln;

    /**
     * Path, in the local file system, to the keystore file.
     *
     * @var string
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

	/**
	 * Original filename, as uploaded in the web form.
	 * 
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

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
     * Set string
     *
     * @param string $string
     * @return Keystore
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get string
     *
     * @return string 
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Keystore
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set pln
     *
     * @param Pln $pln
     * @return Keystore
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
     * Set path
     *
     * @param string $path
     * @return Keystore
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
	
	public function __toString() {
		return $this->filename;
	}
}
