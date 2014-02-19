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
            ->setDescription('Monitor a PLN box or AU')
            ->addArgument('type', InputArgument::OPTIONAL, 'Do you want to monitor a box or an au?')
            ->addArgument('id', InputArgument::OPTIONAL, 'What is its ID?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');

        // Instantiate the monitor.
        $monitor = $this->getContainer()->get('pln_monitor');

        $monitor->boxId = $id;
        $output->writeln($monitor->displayBoxId());
    }
}
