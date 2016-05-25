<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\DepositStatus;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DepositStatusCommand extends ContainerAwareCommand
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
     * @var Box[]
     */
    private $boxes;

    /**
     * @var int
     */
    private $boxCount;

    /**
     * @var AuIdGenerator
     */
    private $idGenerator;

    public function configure()
    {
        $this->setName('lom:deposit:status');
        $this->setDescription('Check that the deposits in LOCKSS have the same checksum.');
        $this->addArgument(
            'plns',
            InputArgument::IS_ARRAY,
            'Database IDs of the PLNs to check.'
        );
        $this->addOption(
            'all',
            '-a',
            InputOption::VALUE_NONE,
            'Process all deposits.'
        );
        $this->addOption(
            'dry-run',
            '-d',
            InputOption::VALUE_NONE,
            'Export only, do not update any internal configs.'
        );
        $this->addOption(
            'limit',
            '-l',
            InputOption::VALUE_OPTIONAL,
            'Limit the number of deposits checked.'
        );
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function loadBoxes(Pln $pln)
    {
        $boxes = $pln->getBoxes();
        $this->boxes = array();
        $this->boxCount = count($boxes);
        foreach ($boxes as $box) {
            try {
                $statusClient = new SoapClient(
                    "http://{$box->getIpAddress()}:{$box->getWebServicePort()}/ws/DaemonStatusService?wsdl",
                    array(
                    'soap_version' => SOAP_1_1,
                    'login'        => $pln->getUsername(),
                    'password'     => $pln->getPassword(),
                    'trace'        => false,
                    'exceptions'   => true,
                    'cache'        => WSDL_CACHE_NONE,
                    )
                );
                $readyResponse = $statusClient->isDaemonReady();
                if ($readyResponse) {
                    $this->boxes[] = $box;
                } else {
                    $this->logger->error("Box {$box->getId()} is not ready.");
                }
            } catch (Exception $e) {
                $this->logger->error($box->getHostname() . '/' . $box->getIpAddress() . ' - ' . $e->getMessage());
                continue;
            }
        }
    }

    protected function checkContent(Box $box, Content $content)
    {
        $auid = $this->idGenerator->fromContent($content);
        $checksumType = $content->getChecksumType();

        $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/HasherService?wsdl";
        $this->logger->notice("Checking content {$content->getId()} on box {$box->getId()}");
        $client = new LockssSoapClient();
        $client->setWsdl($wsdl);
        $client->setOption('login', $box->getPln()->getUsername());
        $client->setOption('password', $box->getPln()->getPassword());
        
        $response = $client->call('hash', array(
            'hasherParams' => array(
                'recordFilterStream' => true,
                'hashType'           => 'V3File',
                'algorithm'          => $checksumType,
                'url'                => $content->getUrl(),
                'auId'               => $auid,
        )));
        if($response === null) {
            $this->logger->warning("{$wsdl} failed.");
            $this->logger->warning($client->getErrors());            
            // do error stuff
            return '*';
        }
        
        if (property_exists($response->return, 'blockFileDataHandler')) {
            $matches = array();
            if (preg_match(
                "/^([a-fA-F0-9]+)\s+http/m",
                $response->return->blockFileDataHandler,
                $matches
            )) {
                $checksumValue = $matches[1];
                return strtoupper($checksumValue);
            } else {
                return '-';
            }
        } else {
            return $response->return->errorMessage;
        }
    }

    protected function checkDeposit(Deposit $deposit)
    {
        $matches = 0;
        $status = array();
        $pln = $deposit->getPln();
        $this->logger->notice("Checking deposit {$deposit->getId()}");
        foreach ($pln->getBoxes() as $box) {
            $status[$box->getId()] = array();
            foreach ($deposit->getContent() as $content) {
                $checksum = $this->checkContent($box, $content);
                $status[$box->getId()][$content->getId()] = $checksum;
                if ($checksum === $content->getChecksumValue()) {
                    $matches++;
                }
            }
        }
        $agreement = $matches / (count($deposit->getContent()) * count($pln->getBoxes()));
        $depositStatus = new DepositStatus();
        $depositStatus->setDeposit($deposit);
        $depositStatus->setQueryDate(new DateTime());
        $depositStatus->setAgreement($agreement);
        $depositStatus->setStatus($status);
        $this->logger->info("Deposit {$deposit->getId()}: " . sprintf("%3.2f%%", ($agreement * 100)));
        return $depositStatus;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');
        $limit = $input->getOption('limit');
        $depositRepository = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');

        if ($input->getOption('all')) {
            $this->logger->notice("Getting all deposits.");
            $deposits = $depositRepository->findAll();
        } else {
            $deposits = $depositRepository->createQueryBuilder('d')
                ->where('d.agreement <> 1')
                ->orWhere('d.agreement is null')
                ->orderBy('d.id')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }

        $this->logger->notice("Found " . count($deposits) . " deposits to check.");
        foreach ($deposits as $deposit) {
            $status = $this->checkDeposit($deposit);
            $deposit->setAgreement($status->getAgreement());
            if ($dryRun) {
                continue;
            }
            $this->em->persist($status);
            $this->em->flush();
        }
    }
}
