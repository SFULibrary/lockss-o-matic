<?php


namespace LOCKSSOMatic\LogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony command to export and optionally purge the log entries.
 */
class ExportLogsCommand extends ContainerAwareCommand
{
    /**
     * Configure the commmand.
     */
    protected function configure() {
        $this->setName('lom:export:logs')->setDescription('Export logs from the database for archiving.')->addArgument(
            'file',
            InputArgument::REQUIRED,
            'File to write the logs to.'
        )->addOption(
            'purge',
            null,
            InputOption::VALUE_NONE,
            'Remove old log entries from the database.'
        );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $actLog = $container->get('activity_log');

        $file = $input->getArgument('file');
        $exists = file_exists($file);
        $header = false;

        if (!$exists) {
            $header = true;
        }

        $fileHandle = fopen($input->getArgument('file'), 'a');
        $csvHandle = $actLog->export($header, $input->getOption('purge'));

        while ($data = fread($csvHandle, 8192)) {
            fwrite($fileHandle, $data, 8192);
        }

        $actLog->log(
            'Logs exported to '.realpath($file).($exists ? ' (appended)' : '')
        );
    }
}
