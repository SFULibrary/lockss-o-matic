<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network plugin import command-line
 */
class PLNPluginImportCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * Configure the command by adding arguments.
     */
    protected function configure()
    {
        $this->setName('lom:import:plnplugin')
            ->setDescription('Import PLN plugins.')
            ->addOption('nocopy', null, InputOption::VALUE_NONE, 'Do not copy the plugin .jar file.')
            ->addArgument('plugin_files', InputArgument::IS_ARRAY, 'Local path to the folder containing the PLN plugin JAR files?');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $jarFiles = array();
        foreacH($input->getArgument('plugin_files') as $path) {
            $jarFiles[] = new SplFileInfo($path);
        }

        $nocopy = false;
        if($input->getOption('nocopy')) {
            $nocopy = true;
        }
        $importer = $this->getContainer()->get('pln_plugin_importer');
        foreach ($jarFiles as $fileInfo) {
            $output->writeln($fileInfo->getFilename());
            try {
                $importer->importJarFile($fileInfo, $nocopy);
            } catch(Exception $e) {
                $output->writeln(" ** " . $e->getMessage());
                if(($p = $e->getPrevious()) !== null) {
                    $output->writeln('  * ' . $p->getMessage());
                }
            }
        }
        $this->em->flush();
    }    
}
