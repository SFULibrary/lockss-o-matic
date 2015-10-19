<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of GenerateAuids
 *
 * @author Michael Joyce <michael@negativespace.net>
 */
class GenerateAuidsCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    public function configure()
    {
        $this->setName('lom:generate:auids')
            ->setDescription('Generate AUids for AUs which do not have one.')
            ->addOption('all', null, InputOption::VALUE_NONE,
                'Generate AUids for all AUs');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->getContainer()->get('activity_log')->disable();
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Au');
        $qb = $repo->createQueryBuilder('a');
        if( ! $input->getOption('all')) {
            $qb->where('a.auid is NULL');
        }
        $iterator = $qb->getQuery()->iterate();
        $n = 0;
        while ($row = $iterator->next()) {
            $au = $row[0];
            $au->generateAuid();
            $n++;
            if ($n % 100 === 0) {
                $this->progressReport($output, $n);
            }
        }
        $this->em->flush();
        $output->writeln("processed {$n} aus");
        $this->getContainer()->get('activity_log')->enable();
    }

    public function progressReport(OutputInterface $output, $n)
    {
        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
        $output->writeln("$n - " . sprintf('%dM', memory_get_usage() / (1024 * 1024)) . '/' . ini_get('memory_limit'));
    }

}
