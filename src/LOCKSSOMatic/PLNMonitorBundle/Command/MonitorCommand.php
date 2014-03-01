<?php

namespace LOCKSSOMatic\PLNMonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\DependencyInjection\LomLogger;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\Boxes;

class MonitorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:monitor')
            ->setDescription('Monitor a PLN, a box or an AU')
            ->addArgument('type', InputArgument::OPTIONAL, 'Do you want to monitor a PLN, a box or an au?')
            ->addArgument('id', InputArgument::OPTIONAL, 'What is its ID?')
            ->addArgument('pause', InputArgument::OPTIONAL, 'Include this if you want to pause between queries.')
            ->addArgument('seconds', InputArgument::OPTIONAL, 'How long to pause between queries (in seconds)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');
        $pause = $input->getArgument('pause');
        $seconds = $input->getArgument('seconds');
        
        // Instantiate an entity manager.
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        // Instantiate a PLN monitor.
        $monitor = $this->getContainer()->get('pln_monitor');
        
        // Instantiate a logger.
        $lomLogger = $this->getContainer()->get('lom_logger');        
        
        /**
         * PLN monitor command.
         */
        // Query all boxes in a specific PLN. Optionally pause for
        // the stated number of seconds between queries.
        if ($type == 'pln' && is_numeric($id)) {
            
            if ($pause && is_numeric($seconds)) {
                $monitor->pause = $pause;
            }
            $monitor->queryPln($id);
        }
        /**
         * Box monitor commands.
         */
        // Query a specific box.
        elseif ($type == 'box' && is_numeric($id)) {
            $lomLogger->log("Mark", "testing query box $id", "Success from within the console command");
            if ($pause && is_numeric($seconds)) {
                $monitor->pause = $pause;
            }
            $monitor->queryBox($id);
        }
        // Query all boxes registered with LOCKSS-O-Matic.
        elseif ($type == 'box' && $id == 'all') {
            $lomLogger->log("Mark", "testing query boxes all", "Success from within the console command");
            if ($pause) {
                $monitor->pause = $pause;
            }
            // Get all boxes and iterate over then, issuing
            // $monitor->queryBox($id) for each one.
            $boxes = $em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Boxes')->findAll();
            foreach ($boxes as $box) {
                $monitor->queryBox($box->getId());
            }
        }
        /**
         * AU monitor commands.
         */
        // Query a specific AU in all boxes it is preserved in.
        elseif ($type == 'au' && is_numeric($id)) {
            $lomLogger->log("Mark", "testing query AU box $id", "Success from within the console command");            
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryAu($id);
        }
        // Query all AUs in all boxes.
        elseif ($type == 'au' && $id == 'all') {
            $lomLogger->log("Mark", "testing query AU all", "Success from within the console command");
            if ($pause) {
                $monitor->pause = $pause;
            }
            // Get all Boxes and iterate over then, issuing
            // $monitor->queryAu($id) for each one.
            $aus = $em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Aus')->findAll();
            foreach ($aus as $au) {
                $monitor->queryAu($au->getId());
            }
        }
        // Query a specific AU in a specific box. In this case, $id
        // will be an AU ID with a Box ID, joined by a comma.
        elseif ($type == 'au' && preg_match('/,/', $id)) {
            list($auId, $boxId) = explode(',', $id);
            $lomLogger->log("Mark", "testing query AU $auId, box $boxId", "Success from within the console command");
            if ($pause) {
                $monitor->pause = $pause;
            }
            $monitor->queryAuOnBox($auId, $boxId);
        }
        else {
            $output->writeln('Usage: php app/console lockssomatic:monitor [pln|box|au] [ID|all] [pause] [SECONDS]');
        }
    }
}
