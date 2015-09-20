<?php

namespace LOCKSSOMatic\ImportExportBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PLNTitleDBImportService {
    
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $titleDbDir;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Logger
     */
    private $logger;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;        
        $this->em = $container->get('doctrine')->getManager();
        $this->fs = new Filesystem();
        $this->titleDbDir = $container->getParameter('lockss_titledb_directory');
        $this->logger = $container->get('logger');
        
        if( ! $this->fs->isAbsolutePath($this->titleDbDir)) {
            $this->titleDbDir = $container->get('kernel')->getRootDir() . '/' . $this->titleDbDir;
        }
        
        if( ! $this->fs->exists($this->titleDbDir)) {
            try {
                $this->logger->warn("Creating directory {$this->titleDbDir}");
                $this->fs->mkdir($this->jarDir);
            } catch (Exception $ex) {
                $this->logger->error("Error creating directory {$this->titleDbDir}.");
                $this->logger->error($ex);
                return false;
            }
        }
        return true;        
    }
    
}