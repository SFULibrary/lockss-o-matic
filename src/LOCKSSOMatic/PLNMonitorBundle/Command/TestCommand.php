<?php

namespace LOCKSSOMatic\PLNMonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:test')
            ->setDescription('Testing custom commands')
            ->addArgument('lockssbox', InputArgument::OPTIONAL, 'What box do you want to monitor?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $box = $input->getArgument('lockssbox');
        if ($box) {
            $text = 'Monitoring '.$box;
        } else {
            $text = 'No box to monitor';
        }

        $output->writeln($text);
    }
}
