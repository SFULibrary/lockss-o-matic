<?php

namespace LOCKSSOMatic\CoreBundle\Services;

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;
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
     * @var string
     */
    private $downloadDir;

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
     * Set the service logger.
     *
     * @param Logger $logger The logger service.
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the kernel environment.
     *
     * @param string $env The kernel environment.
     */
    public function setKernelEnv($env) {
        $this->env = $env;
    }

    /**
     * Set the download directory for content fetched from the PLN and made
     * available to the staging server.
     *
     * @param string $dir The file path to the download directory.
     */
    public function setDownloadDir($dir) {
        $this->downloadDir = $dir;
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

    /**
     * Get the file path for the cache directory.
     *
     * @return string
     */
    public function getCacheDownloadDir() {
        $path = implode('/', array(
            $this->getRootPath(),
            $this->downloadDir,
        ));
        return $path;
    }

    /**
     * Get the file path for a downloadable content item. LOCKSS boxes do not
     * download content from LOM. This is for the staging server to fetch
     * content from the PLN.
     *
     * @param Content $content The content in question.
     *
     * @return string
     */
    public function getDownloadContentPath(Content $content) {
        $path = implode('/', array(
            $this->getCacheDownloadDir(),
            $content->getDeposit()->getUuid(),
            $content->getId(),
            basename($content->getUrl())
        ));
        return $path;
    }

    /**
     * Get the root directory for lockss files.
     *
     * @return string
     */
    public function getLockssDir() {
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
    public function getPluginsDir() {
        $path = implode('/', array(
            $this->getLockssDir(),
            'plugins',
        ));

        return $path;
    }

    /**
     * Get the path to exported lockss configuration files.
     *
     * @param Pln $pln Get the config directory for this PLN.
     *
     * @return string
     */
    public function getConfigsDir(Pln $pln) {
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
     * @param Pln $pln Get the XML file for this PLN.
     *
     * @return string
     */
    public function getLockssXmlFile(Pln $pln) {
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
     * @param Pln $pln get the plugins export path for this PLN.
     *
     * @return string
     */
    public function getPluginsExportDir(Pln $pln) {
        $path = implode('/', array(
            $this->getConfigsDir($pln),
            'plugins',
        ));

        return $path;
    }

    /**
     * Get the path for one exported plugin in a PLN.
     *
     * @param Pln $pln Get the plugins export file for this PLN.
     * @param Plugin $plugin Get the export path for this plugin.
     *
     * @return string
     */
    public function getPluginsExportFile(Pln $pln, Plugin $plugin) {
        $path = implode('/', array(
            $this->getPluginsExportDir($pln),
            $plugin->getFilename(),
        ));

        return $path;
    }

    /**
     * Get the path to the manifest file for the plugins in a PLN.
     *
     * @param Pln $pln The PLN providing the manifest.
     *
     * @return string
     */
    public function getPluginsManifestFile(Pln $pln) {
        $path = implode('/', array(
            $this->getPluginsExportDir($pln),
            'index.html',
        ));

        return $path;
    }

    /**
     * Get the path to the manifests for a PLN.
     *
     * @param Pln $pln The PLN providing the manifest.
     * @param ContentProvider $provider The content provider for the manifest.
     *
     * @return string
     */
    public function getManifestDir(Pln $pln, ContentProvider $provider) {
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
     * @param Au $au The AU providing the manifest.
     *
     * @return string
     */
    public function getManifestPath(Au $au) {
        $path = implode('/', array(
            $this->getManifestDir($au->getPln(), $au->getContentprovider()),
            'manifest_'.$au->getId().'.html',
        ));

        return $path;
    }

    /**
     * Get the path to the titles database directory.
     *
     * @param Pln $pln The PLN providing the TitleDB.
     * @param ContentProvider $provider The content provider for the titledb.
     * @return string
     */
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
