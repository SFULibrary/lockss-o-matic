<?php

namespace LOCKSSOMatic\LoggingBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportLogsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('lom:export:logs')
            ->setDescription('Export logs from the database for archiving.')
            ->addArgument('file', InputArgument::REQUIRED, 'File to write the logs to.')
            ->addOption('purge', null, InputOption::VALUE_NONE,'Remove old log entries from the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $actLog = $container->get('activity_log');
        
        $file = $input->getArgument('file');
        $exists = file_exists($file);
        
        $actLog->log(
            'Export logs to file', 
            'info',
            'Logs exported to ' . realpath($file) . ($exists ? ' (appended)' : '')
        );
        
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $results = $em->getRepository('LOCKSSOMaticLoggingBundle:LogEntry')
            ->findBy(array(), array('id' => 'ASC'));
        $handle = fopen($input->getArgument('file'), 'a');
        fputcsv($handle, LogEntry::toArrayHeader());
        foreach ($results as $entry) {
            fputcsv($handle, $entry->toArray());
            if ($input->getOption('purge')) {
                $em->remove($entry);
            }
            $em->detach($entry);
        }
        if($input->getOption('purge')) {
            $actLog->log(
                'Log entries purged from the database.',
                'info',
                'Logs exported to ' . realpath($file) . ($exists ? ' (appended)' : '')
            );
        }
        $em->flush();
    }

}
