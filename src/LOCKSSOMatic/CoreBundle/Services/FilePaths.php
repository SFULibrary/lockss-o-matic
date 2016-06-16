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

namespace LOCKSSOMatic\CoreBundle\Services;

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Construct file paths for different elements of the application.
 */
class FilePaths
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * The Kernel environment.
     * 
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
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * Set the service logger.
     * 
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the kernel environment.
     * 
     * @param string $env
     */
    public function setKernelEnv($env)
    {
        $this->env = $env;
    }

    /**
     * Get the root file system path.
     * 
     * @return string
     */
    public function getRootPath()
    {
        $path = dirname($this->env);

        return realpath($path);
    }

    /**
     * Get the root directory for lockss files.
     * 
     * @return string
     */
    public function getLockssDir()
    {
        $path = implode('/', array(
            $this->getRootPath(),
            'data',
            'lockss',
        ));

        return $path;
    }

    /**
     * Ge the directory for uploaded plugin files.
     * 
     * @return string
     */
    public function getPluginsDir()
    {
        $path = implode('/', array(
            $this->getLockssDir(),
            'plugins',
        ));

        return $path;
    }

    /**
     * Get the path to exported lockss configuration files.
     * 
     * @param Pln $pln
     * 
     * @return string
     */
    public function getConfigsDir(Pln $pln)
    {
        $path = implode('/', array(
            $this->getRootPath(),
            'data',
            'plnconfigs',
            $pln->getId(),
        ));

        return $path;
    }

    /**
     * Get the complete path to the export lockss.xml file for one PLN.
     * 
     * @param Pln $pln
     * 
     * @return string
     */
    public function getLockssXmlFile(Pln $pln)
    {
        $path = implode('/', array(
            $this->getConfigsDir($pln),
            'properties',
            'lockss.xml',
        ));

        return $path;
    }

    /**
     * Get the directory for exported plugins for a PLN.
     * 
     * @param Pln $pln
     * 
     * @return string
     */
    public function getPluginsExportDir(Pln $pln)
    {
        $path = implode('/', array(
            $this->getConfigsDir($pln),
            'plugins',
        ));

        return $path;
    }

    /**
     * Get the path for one exported plugin in a PLN.
     * 
     * @param Pln $pln
     * @param Plugin $plugin
     * 
     * @return string
     */
    public function getPluginsExportFile(Pln $pln, Plugin $plugin)
    {
        $path = implode('/', array(
            $this->getPluginsExportDir($pln),
            $plugin->getFilename(),
        ));

        return $path;
    }

    /**
     * Get the path to the manifest file for the plugins in a PLN.
     * 
     * @param Pln $pln
     * 
     * @return string
     */
    public function getPluginsManifestFile(Pln $pln)
    {
        $path = implode('/', array(
            $this->getPluginsExportDir($pln),
            'index.html',
        ));

        return $path;
    }

    /**
     * Get the path to the manifests for a PLN.
     * 
     * @param Pln $pln
     * @param ContentProvider $provider
     * @return string
     */
    public function getManifestDir(Pln $pln, ContentProvider $provider)
    {
        $path = implode('/', array(
            $this->getConfigsDir($pln),
            'manifests',
            $provider->getContentOwner()->getId(),
            $provider->getId(),
        ));

        return $path;
    }

    /**
     * Get the path to a manifest for an AU.
     * 
     * @param Au $au
     * 
     * @return string
     */
    public function getManifestPath(Au $au)
    {
        $path = implode('/', array(
            $this->getManifestDir($au->getPln(), $au->getContentprovider()),
            'manifest_'.$au->getId().'.html',
        ));

        return $path;
    }

    /**
     * Get the path to the titles database directory.
     * 
     * @param Pln $pln
     * @param ContentProvider $provider
     * @return string
     */
    public function getTitleDbDir(Pln $pln, ContentProvider $provider)
    {
        $path = implode('/', array(
            $this->getConfigsDir($pln),
            'titledbs',
            $provider->getContentOwner()->getId(),
            $provider->getId(),
        ));

        return $path;
    }
}
