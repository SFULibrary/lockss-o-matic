<?php

namespace LOCKSSOMatic\PLNMonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:monitor')
            ->setDescription('Monitor a PLN, a box or an AU')
            ->addArgument('type', InputArgument::OPTIONAL, 'Do you want to monitor a PLN, a box or an au?')
            ->addArgument('id', InputArgument::OPTIONAL, 'What is its ID?')
            ->addArgument('pause', InputArgument::OPTIONAL, 'If a PLN, how long to pause between queries (in seconds)')
            ->addArgument('au_box_id', InputArgument::OPTIONAL, 'If an AU, do you want to query only one box?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');
        $au_box_id = $input->getArgument('au_box_id');
        $pause = $input->getArgument('pause');

        // Instantiate the monitor.
        $monitor = $this->getContainer()->get('pln_monitor');
        
        if ($type == 'pln') {
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryPln($id);
        }
        elseif ($type == 'pln' && $id == 'all') {
            if ($pause) {
                $monitor->pause = $pause;
            }
            // @todo: Get all AUs and iterate over then, issuing
            // $monitor->queryPln($id) for each one.
        }        
        elseif ($type == 'box') {
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryBox($id);
        }
        elseif ($type == 'box' && $id == 'all') {
            if ($pause) {
                $monitor->pause = $pause;
            }
            // @todo: Get all boxes and iterate over then, issuing
            // $monitor->queryBox($id) for each one.
        }        
        elseif ($type == 'au') {
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryAu($id);
        }
        elseif ($type == 'au' && $au_box_id) {
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryAuOnBox($id, $au_box_id);
        }
        else {
            $output->writeln('Sorry, not enough information to do anything.');
        }
    }
}
