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
            ->addArgument('pause', InputArgument::OPTIONAL, 'How long to pause between queries (in seconds)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');
        $pause = $input->getArgument('pause');

        // Instantiate the monitor.
        $monitor = $this->getContainer()->get('pln_monitor');
        
        if ($input->getArgument('type') == 'pln') {
            $monitor->plnId = $id;
            if ($pause) {
                $monitor->pause = $pause;
            }
            $output->writeln($monitor->queryPln());
        }
        
        if ($input->getArgument('type') == 'box') {
            $monitor->boxId = $id;
            $output->writeln($monitor->queryBox());
        }        
        
        
    }
}
