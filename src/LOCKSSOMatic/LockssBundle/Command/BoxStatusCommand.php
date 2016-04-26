<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use Monolog\Logger;
use SoapClient;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BoxStatusCommand extends ContainerAwareCommand
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
        $this->setName('lom:box:status');
        $this->setDescription('Check the status of the LOCKSS AUs');
        $this->addOption(
            'dry-run',
            '-d',
            InputOption::VALUE_NONE,
            'Export only, do not update any internal configs.'
        );
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function checkBox(Pln $pln, Box $box)
    {
        $statusClient = new SoapClient(
            "http://{$box->getIpAddress()}:{$box->getWebServicePort()}/ws/DaemonStatusService?wsdl",
            array(
            'soap_version' => SOAP_1_1,
            'login'        => $pln->getUsername(),
            'password'     => $pln->getPassword(),
            'trace'        => true,
            'exceptions'   => true,
            'cache'        => WSDL_CACHE_NONE,
            )
        );
        $spacesResponse = $statusClient->queryRepositorySpaces(array('repositorySpaceQuery' => 'SELECT *'));
        return get_object_vars($spacesResponse->return);
    }

    /**
     * @return Pln
     * @param array|null $plnIds
     */
    protected function getPlns($plnIds = null)
    {
        $pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find(1);
        return array($pln);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $plns = $this->getPlns();
        foreach ($plns as $pln) {
            foreach ($pln->getBoxes() as $box) {
                $this->logger->notice("Checking {$box->getHostname()}");
                $boxStatus = new BoxStatus();
                $boxStatus->setBox($box);
                $boxStatus->setQueryDate(new DateTime());
                try {
                    $status = $this->checkBox($pln, $box);
                    $boxStatus->setStatus($status);
                    $boxStatus->setSuccess(true);
                } catch (Exception $e) {
                    $this->logger->error("Cannot get status of {$box->getHostname()}: {$e->getMessage()}");
                    $boxStatus->setSuccess(false);
                    $boxStatus->setStatus(array(
                        'error' => $e->getMessage(),
                    ));
                }
                if ($input->getOption('dry-run')) {
                    continue;
                }
                $this->em->persist($boxStatus);
                $this->em->flush();
            }
        }
    }
}
