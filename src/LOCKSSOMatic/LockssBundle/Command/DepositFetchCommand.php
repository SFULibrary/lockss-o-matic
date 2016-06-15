<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DepositFetchCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AuIdGenerator
     */
    private $idGenerator;
    
    public function configure()
    {
        $this->setName('lom:deposit:fetch');
        $this->setDescription('Fetch one or more deposits from the PLN.');
        $this->addArgument('uuids', InputArgument::IS_ARRAY, 'One or more deposit UUIDs to fetch.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }
    
    /**
     * Gets the boxes for a PLN in a random order.
     * 
     * @return Box[]
     */
    public function loadBoxes(Pln $pln) {
        $boxes = $pln->getBoxes()->toArray();
        shuffle($boxes);
        return $boxes;
    }

    protected function getDeposits($uuids) {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        return $repo->findBy(array('uuid' => $uuids));
    }
    
    protected function fetchDeposit(Deposit $deposit) {
        $pln = $deposit->getPln();
        $boxes = $this->loadBoxes($pln);
        $auid = $this->idGenerator->fromAu($deposit->getContent()->first()->getAu());
        
        foreach($deposit->getContent() as $content) {
            
        }
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $uuids = $input->getArgument('uuids');        
        $deposits = $this->getDeposits($uuids);
        $this->logger->notice("Fetching " . count($deposits) . " deposit(s)");
        
        foreach($deposits as $deposit) {
            $this->logger->notice("Fetching {$deposit->getUuid()}");
            $result = $this->fetchDeposit($deposit);
        }
    }

}