<?php

namespace LOCKSSOMatic\LoggingBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportLogsCommand extends ContainerAwareCommand {
    
    protected function configure() {
        $this->setName('lom:export:logs')
            ->setDescription('Export logs from the database for archiving.')
            ->addArgument('file', InputArgument::REQUIRED, 'File to write the logs to.')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Remove old log entries from the database.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $results = $em->getRepository('LOCKSSOMaticLoggingBundle:LogEntry')
            ->findBy(array(), array('id' => 'ASC'));
        $handle = fopen($input->getArgument('file'), 'a');
        fputcsv($handle, LogEntry::toArrayHeader());
        foreach($results as $entry) {
           fputcsv($handle, $entry->toArray());
           if($input->getOption('purge')) {
               $em->remove($entry);
           }
           $em->detach($entry);
        }
        $em->flush();
    }
}