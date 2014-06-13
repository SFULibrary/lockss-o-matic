<?php
// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNImportCommand.php
namespace LOCKSSOMatic\PLNImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\DependencyInjection\LomLogger;


use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;

class PLNImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:plnimport')
            ->setDescription('Import PLN plugins - using return value for testing.')
            ->addArgument('plugin_folder_path', InputArgument::OPTIONAL, 'Local path to the folder containing the PLN plugin JAR files?')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path_to_plugins = $input->getArgument('plugin_folder_path');
        if ($path_to_plugins){
            $num_jar_files = $this->countJarFilesInDir($path_to_plugins);
            $text = "There are $num_jar_files plugin JAR files in the provided directory.";
        } else {
            $text = 'No path was provided.  Aborting PLN plugin import.';
        }
        
        $output->writeln($text);
    }
    
    // methods related to ingestion of Lockss Plugins
    protected function countJarFilesInDir($dir_path)
    {
      $count = 0;
      $iterator = new \DirectoryIterator($dir_path);
      foreach ($iterator as $fileinfo) {
          $jar_string = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
          if($fileinfo->isFile() & $jar_string == 'jar'){
            $count +=1;
          }
      }
     return $count;
    } 
    
}