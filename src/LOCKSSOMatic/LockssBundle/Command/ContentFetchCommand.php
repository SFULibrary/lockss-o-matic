<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\Services\FilePaths;
use LOCKSSOMatic\LockssBundle\Services\ContentFetcherService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Fetch a content item from the PLN.
 *
 * @todo this doesn't seem finished.
 */
class ContentFetchCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ContentFetcherService
     */
    private $fetcher;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var FilePaths
     */
    private $fp;

    /**
     * {@inheritdoc}
     */
    public function configure() {
        $this->setName('lom:content:fetch');
        $this->setDescription('Fetch one or more deposits from the PLN.');
        $this->addArgument('uuids', InputArgument::IS_ARRAY, 'One or more deposit UUIDs to fetch.');
    }

    /**
     * {@inheritdoc}
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->fetcher = $this->getContainer()->get('lockss.content.fetcher');
        $this->fs = new Filesystem();
        $this->fp = $this->getContainer()->get('lom.filepaths');
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $content = $this->em->find('LOCKSSOMaticCrudBundle:Content', 1982);
        $file = $this->fetcher->fetch($content);
        if($file === null) {
            return;
        }
        $path = $this->fp->getDownloadContentPath($content);
        $dir = dirname($path);
        if(!file_exists($dir)) {
            $this->fs->mkdir($dir);
        }
        $fh = fopen($path, 'wb');
        while($data = fread($file, 64 * 1024)) {
            fwrite($fh, $data);
        }
    }
}
