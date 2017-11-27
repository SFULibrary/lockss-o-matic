<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\Services\FilePaths;
use LOCKSSOMatic\LockssBundle\Services\ContentFetcherService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Fetch a content item from the PLN.
 *
 * @todo this doesn't seem finished.
 */
class ContentFetchCommand extends ContainerAwareCommand {

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
        $this->addOption('boxId', null, InputOption::VALUE_REQUIRED, "Use this box ID");
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, "Use this username.");
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, "Use this password.");
        $this->addArgument('ids', InputArgument::IS_ARRAY, 'One or more content URL database IDs to fetch.');
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
        $boxId = $input->getOption('boxId');
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Content');
        $contentItems = $repo->findBy(array(
            'id' => $input->getArgument('ids'),
        ));
        $this->logger->notice("Fetching " . count($contentItems) . " items(s)");        
        foreach ($contentItems as $content) {
        $this->logger->notice("downloading {$content->getId()}");        
            $file = $this->fetcher->fetch($content, $boxId, $username, $password);
            if ($file === null) {
                $this->logger->error("Cannot download content item " . $content->getId());
                continue;
            }
            $path = $this->fp->getDownloadContentPath($content);
            $dir = dirname($path);
            if (!file_exists($dir)) {
                $this->fs->mkdir($dir);
            }
            $fh = fopen($path, 'wb');
            while ($data = fread($file, 64 * 1024)) {
                fwrite($fh, $data);
            }
        }
    }

}
