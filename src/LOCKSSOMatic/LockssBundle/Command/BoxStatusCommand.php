<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use Monolog\Logger;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
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
        $this->addArgument(
            'boxes', InputArgument::IS_ARRAY,
            'Optional list of box ids to check.'
        );
        $this->addOption(
            'dry-run', '-d', InputOption::VALUE_NONE,
            'Do not update box status, just report results to console.'
        );
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function checkBox(Box $box)
    {
        $pln = $box->getPln();
        $statusClient = new SoapClient(
            "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/DaemonStatusService?wsdl",
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

    protected function getBoxes($boxIds = null)
    {
        if ($boxIds === null || count($boxIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findAll();
        }
        return $this->em->getRepository('LOCKSSOMaticCrudBundle:Box')->findById($boxIds);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $boxIds = $input->getArgument('boxes');
        foreach ($this->getBoxes($boxIds) as $box) {
            $this->logger->notice("Checking {$box->getHostname()}");
            $boxStatus = new BoxStatus();
            $boxStatus->setBox($box);
            $boxStatus->setQueryDate(new DateTime());
            try {
                $status = $this->checkBox($box);
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
