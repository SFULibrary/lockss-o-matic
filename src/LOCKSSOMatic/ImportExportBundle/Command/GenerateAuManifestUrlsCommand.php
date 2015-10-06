<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\Common\Util\Debug;
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
class GenerateAuManifestUrlsCommand extends ContainerAwareCommand
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
        $this->setName('lom:generate:manifest-urls')
            ->setDescription('Generate AUids for AUs which do not have one.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->getContainer()->get('activity_log')->disable();

        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Au');
        $q = $repo->createQueryBuilder('a')
            ->where('a.manifestUrl is NULL')
            ->getQuery();
        $iterator = $q->iterate();
        
        $n = 0;

        while (($row = $iterator->next())) {
            $au = $row[0];
            $output->writeln($au->generateManifestUrl());
            $n++;
            if ($n % 100 === 0) {
                $this->progressReport($output, $n);
            }
        }
        $this->progressReport($output, $n);
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
