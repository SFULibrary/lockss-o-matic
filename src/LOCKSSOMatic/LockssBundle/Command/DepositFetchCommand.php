<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\DepositStatus;
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
     * 
     * @param type $all
     * @param type $limit
     * @return Deposit[]
     */
    protected function getDeposits($uuids) {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        return $repo->findBy(array('uuid' => $uuids));
    }
    
    protected function fetchDeposit(Deposit $deposit) {
        $auid = $this->idGenerator->fromAu($deposit->getContent()->first()->getAu());
        $box = $this->em->find('LOCKSSOMaticCrudBundle:Box', 9); // 9 === localhost:8081
        $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/ContentService?wsdl";
        $client = new LockssSoapClient();
        $client->setWsdl($wsdl);
        $client->setOption('login', $box->getPln()->getUsername());
        $client->setOption('password', $box->getPln()->getPassword());
        foreach($deposit->getContent() as $content) {
            $this->logger->notice("fetching {$content->getUrl()}");
            $response = $client->call('fetchFile', array(
                'auid' => $auid,
                'url' => $content->getUrl(),
            ), true);
            if($response === null) {
                dump("CLIENT_ERRORS:");
                dump($client->getErrors());
            }
            dump($response);
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