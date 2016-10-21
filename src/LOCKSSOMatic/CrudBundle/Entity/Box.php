<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A box in a network.
 *
 * @ORM\Table(name="boxes")
 * @ORM\Entity
 */
class Box implements GetPlnInterface
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
     * The DNS name.
     *
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255, nullable=false)
     */
    private $hostname;

    /**
     * The protocol to use in the lockss.xml file. Defaults to TCP.
     *
     * @var string
     * @ORM\Column(name="protocol", type="string", length=16, nullable=false)
     */
    private $protocol;

    /**
     * The port used for the lockss.xml file.
     *
     * @var int
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port;

    /**
     * The port to use for webservice requests - usually :80, but may be 
     * different for testing.
     *
     * @var int
     * @ORM\Column(name="ws_port", type="integer", nullable=false) 
     */
    private $webServicePort;

    /**
     * The box's IP address. The class will resolve it automatically from the
     * domain name if the ipAddress is null or blank.
     *
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=16, nullable=false)
     */
    private $ipAddress;

    /**
     * The PLN this box is a part of.
     *
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="boxes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id", onDelete="RESTRICT", nullable=false)
     * })
     */
    private $pln;

    /**
     * Timestamped list of box status query results.
     *
     * @ORM\OneToMany(targetEntity="BoxStatus", mappedBy="box", orphanRemoval=true)
     *
     * @var Collection|BoxStatus
     */
    private $status;

    /**
     * Build a new box, with protocol set to TCP, port 9729, and web service
     * port 8080.
     */
    public function __construct()
    {
        $this->status = new ArrayCollection();
        $this->protocol = 'TCP';
        $this->port = 9729;
        $this->webServicePort = 80;
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
     * Set hostname.
     *
     * @param string $hostname
     *
     * @return Box
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set ipAddress.
     *
     * @param string $ipAddress
     *
     * @return Box
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set pln.
     *
     * @param Pln $pln
     *
     * @return Box
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;
        $pln->addBox($this);

        return $this;
    }

    /**
     * Get pln.
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Add status.
     *
     * @param BoxStatus $status
     *
     * @return Box
     */
    public function addStatus(BoxStatus $status)
    {
        $this->status[] = $status;

        return $this;
    }

    /**
     * Remove status.
     *
     * @param BoxStatus $status
     */
    public function removeStatus(BoxStatus $status)
    {
        $this->status->removeElement($status);
    }

    /**
     * @return BoxStatus
     */
    public function getCurrentStatus()
    {
        return $this->status->last();
    }

    /**
     * Get status.
     *
     * @return Collection|BoxStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set protocol.
     *
     * @param string $protocol
     *
     * @return Box
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol.
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set port.
     *
     * @param int $port
     *
     * @return Box
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Resolve the hostname into an ipAddress and save it. Called automatically
     * when saving the box via doctrine.
     *
     * @param bool $force force the update, even if the ip is already known.
     */
    public function resolveHostname($force = false)
    {
        if ($force === true || $this->ipAddress === null || $this->ipAddress === '') {
            $ip = gethostbyname($this->hostname);
            if ($ip !== $this->hostname) {
                $this->ipAddress = $ip;
            }
        }
    }

    /**
     * Set webServicePort.
     *
     * @param int $webServicePort
     *
     * @return Box
     */
    public function setWebServicePort($webServicePort)
    {
        $this->webServicePort = $webServicePort;

        return $this;
    }

    /**
     * Get webServicePort.
     *
     * @return int
     */
    public function getWebServicePort()
    {
        return $this->webServicePort;
    }
}
