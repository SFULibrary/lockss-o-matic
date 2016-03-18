<?php

namespace LOCKSSOMatic\CoreBundle\Services;

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

class FilePaths {
	
	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $env;
	
	/**
	 * @var Filesystem
	 */
	private $fs;
	
    /**
     * Build the service.
     */
    public function __construct() {
        $this->fs = new Filesystem();
    }
    
    /**
     * Set the service logger
     * 
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the kernel environment.
     * 
     * @param string $env
     */
    public function setKernelEnv($env) {
        $this->env = $env;
    }
    
	/**
	 * Get the root file system path.
	 * 
	 * @return string
	 */
	public function getRootPath() {
		$path = dirname($this->env);
		return realpath($path);
	}
	
	public function getLockssDir() {
		$path = implode('/', array(
			$this->getRootPath(),
			'lockss',
		));
		return $path;
	}
	
	public function getPluginsDir() {
		$path = implode('/', array(
			$this->getLockssDir(),
			'plugins'
		));
		return $path;
	}
	
	public function getConfigsDir(Pln $pln) {
		$path = implode('/', array(
			$this->getRootPath(),
			'data',
			'plnconfigs',
			$pln->getId(),
		));
		return $path;
	}
	
	public function getLockssXmlFile(Pln $pln) {
		$path = implode('/', array(
			$this->getConfigsDir(),
			'properties',
			'lockss.xml'
		));
		return $path;
	}
	
	public function getPluginsExportDir(Pln $pln) {
		$path = implode('/', array(
			$this->getConfigsDir($pln),
			'plugins'
		));
		return $path;
	}
	
	public function getPluginsExportFile(Pln $pln, Plugin $plugin) {
		$path = implode('/', array(
			$this->getPluginsExportDir($pln),
			$plugin->getFilename(),
		));
		return $path;
	}
	
	public function getPluginsManifestFile(Pln $pln) {
		$path = implode('/', array(
			$this->getPluginsExportDir($pln),
			'index.html'
		));
		return $path;
	}
	
	public function getManifestDir(Pln $pln, ContentProvider $provider) {
		$path = implode('/', array(
			$this->getConfigsDir($pln),
			'manifests',
			$provider->getContentOwner()->getId(),
			$provider->getId(),
		));
		return $path;
	}
	
	public function getManifestPath(Au $au) {
		$path = implode('/', array(
			$this->getManifestDir($au->getPln(), $au->getContentprovider()),
			$au->getId() . '.html'
		));
		return $path;
	}
	
	public function getTitleDbDir(Pln $pln, ContentProvider $provider) {
		$path = implode('/', array(
			$this->getConfigsDir($pln),
			'titledbs',
			$provider->getContentOwner()->getId(),
			$provider->getId(),			
		));
		return $path;
	}
}