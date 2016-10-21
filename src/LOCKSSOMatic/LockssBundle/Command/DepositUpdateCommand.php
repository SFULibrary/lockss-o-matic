<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DepositUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    public function configure()
    {
        $this->setName('lom:deposit:update');
        $this->setDescription('Update content checksums with values from the PLN.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    /**
     * @param type $all
     * @param type $limit
     *
     * @return Deposit[]
     */
    protected function getDeposits()
    {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        $qb = $repo->createQueryBuilder('d');
            $qb->where('d.agreement <> 1');
            $qb->andWhere('d.agreement is not null');

        return $qb->getQuery()->getResult();
    }

    protected function queryDeposit(Deposit $deposit)
    {
        $status = array_values($deposit->getStatus()->last()->getStatus());
        $counts = array();
        unset($status[0]['expected']);
        foreach($status[0] as $k => $v) {
            if( ! array_key_exists($v, $counts)) {
                $counts[$v] = 0;
            }
            $counts[$v]++;
        }
        if(count($counts) !== 1 || array_key_exists('*', $counts)) {
            return;
        }
        reset($counts);
        $sum = key($counts);
        $deposit->getContent()->first()->setChecksumValue($sum);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $deposits = $this->getDeposits();
        $this->logger->notice('Updating deposit status for '.count($deposits).' deposits.');

        foreach ($deposits as $deposit) {
            $result = $this->queryDeposit($deposit);
            $this->logger->notice("{$deposit->getPln()->getId()} - {$result[0]} - {$deposit->getUUid()}");
            $this->em->flush();
        }
    }
}
