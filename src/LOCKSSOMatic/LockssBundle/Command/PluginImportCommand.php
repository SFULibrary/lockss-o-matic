<?php


namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use Monolog\Logger;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network plugin import command-line.
 */
class PluginImportCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * Configure the command by adding arguments.
     */
    protected function configure() {
        $this->setName('lom:import:plugin')->setDescription('Import PLN plugins.')->addOption(
            'nocopy',
            null,
            InputOption::VALUE_NONE,
            'Do not copy the plugin .jar file.'
        )->addArgument(
            'plugin_files',
            InputArgument::IS_ARRAY,
            'Local path to the folder containing the PLN plugin JAR files?'
        );
    }

    /**
     * Get a logger to do some logging.
     *
     * @return Logger
     */
    protected function getLogger() {
        return $this->getContainer()->get('logger');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     */
    protected function execute(InputInterface $input) {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();

        /** @var Logger $logger */
        $logger = $this->getLogger();

        $jarFiles = array();
        foreach ($input->getArgument('plugin_files') as $path) {
            $jarFiles[] = new SplFileInfo($path);
        }

        $copy = true;
        if ($input->getOption('nocopy')) {
            $copy = false;
        }

        $importer = $this->getContainer()->get('pln_plugin_importer');
        foreach ($jarFiles as $fileInfo) {
            $logger->notice("Importing {$fileInfo->getFilename()}");
            try {
                $importer->importJarFile($fileInfo, $copy);
            } catch (Exception $e) {
                $logger->error("Import error: {$e->getMessage()}");
                if (($p = $e->getPrevious()) !== null) {
                    $logger->error($p->getMessage());
                }
            }
            $this->em->flush();
        }
    }
}
