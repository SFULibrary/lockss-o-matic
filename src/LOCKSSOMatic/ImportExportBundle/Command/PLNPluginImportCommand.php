<?php

// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNPluginImportCommand.php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use DirectoryIterator;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperty;
use LOCKSSOMatic\CRUDBundle\Entity\Plugin;
use SimpleXMLElement;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ZipArchive;

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
            ->addArgument('plugin_folder_path', InputArgument::REQUIRED, 'Local path to the folder containing the PLN plugin JAR files?');
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
        $pathToPlugins = $input->getArgument('plugin_folder_path');
        $jarFiles = $this->getJarFiles($pathToPlugins);

        $output->writeln("There are " . count($jarFiles) . " JAR files in the directory.");
        $importer = $this->getContainer()->get('pln_plugin_importer');
        foreach ($jarFiles as $fileInfo) {
            $output->writeln($fileInfo->getFilename());
            try {
                $importer->importJarFile($fileInfo);
            } catch(Exception $e) {
                $output->writeln(" ** " . $e->getMessage());
                if(($p = $e->getPrevious()) !== null) {
                    $output->writeln('  * ' . $p->getMessage());
                }
            }
        }
        $this->em->flush();
    }
    
    /**
     * Get the JAR files in a directory.
     *
     * @param type $dirPath
     *
     * @throws UnexpectedValueException if the path cannot be opened.
     * @throws RuntimeException if the path is an empty string.
     *
     * @return SplFileInfo[]
     */
    protected function getJarFiles($dirPath)
    {
        $files = array();
        $iterator = new DirectoryIterator($dirPath);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->getExtension() === 'jar') {
                $files[] = $fileInfo->getFileInfo();
            }
        }
        return $files;
    }

}
