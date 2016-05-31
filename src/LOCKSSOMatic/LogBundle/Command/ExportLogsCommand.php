<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
    protected function configure()
    {
        $this->setName('lom:export:logs')
            ->setDescription('Export logs from the database for archiving.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'File to write the logs to.'
            )
            ->addOption(
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
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
