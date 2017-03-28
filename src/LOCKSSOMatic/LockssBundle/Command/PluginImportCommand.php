<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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
     * {@inheritDocs}
     */
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
        $this->setName('lom:import:plugin')
            ->setDescription('Import PLN plugins.')
            ->addOption(
                'nocopy',
                null,
                InputOption::VALUE_NONE,
                'Do not copy the plugin .jar file.'
            )
            ->addArgument(
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
    protected function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
