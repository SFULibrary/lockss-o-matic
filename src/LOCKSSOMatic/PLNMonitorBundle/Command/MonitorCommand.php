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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');

        // Instantiate the monitor.
        $monitor = $this->getContainer()->get('pln_monitor');
        
        if ($type == 'pln') {
            $monitor->plnId = $id;
            $output->writeln($monitor->queryPln());
        }
    }
}
