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
            ->addArgument('file', InputArgument::REQUIRED,
                'File to write the logs to.')
            ->addOption('purge', null, InputOption::VALUE_NONE,
                'Remove old log entries from the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $actLog = $container->get('activity_log');

        $file = $input->getArgument('file');
        $exists = file_exists($file);
        $header = false;

        if (!$exists) {
            touch($file); # realpath requires the file to exist.
            $header = true;
        }

        $fileHandle = fopen($input->getArgument('file'), 'a');
        $csvHandle = $actLog->export($header, $input->getOption('purge'));

        while($data = fread($csvHandle, 8192)) {
            fwrite($fileHandle, $data, 8192);
        }

        $actLog->log(
            'Logs exported to ' . realpath($file) . ($exists ? ' (appended)' : '')
        );
    }

}
