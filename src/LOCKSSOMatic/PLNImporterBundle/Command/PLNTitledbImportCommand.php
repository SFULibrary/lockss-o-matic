<?php
// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNTitledbImportCommand.php
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

/**
 * Private Lockss network plugin import command-line
 */
class PLNTitledbImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:plntitledbimport')
            ->setDescription('Import PLN titldb file - using return value for testing.')
            ->addArgument('path_to_titledb', InputArgument::OPTIONAL, 'Local path to the titledb xml file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToTitledb = $input->getArgument('path_to_titledb');
        if ($pathToTitledb) {
            $text = $pathToTitledb;
            $output->writeln($text);
        } else {
            $text = 'No path was provided.  Aborting PLN plugin import.';
        }

        //$output->writeln($text);
    }

} // end of class