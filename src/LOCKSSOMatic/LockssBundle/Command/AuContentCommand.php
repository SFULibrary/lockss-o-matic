<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use Monolog\Logger;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuContentCommand extends ContainerAwareCommand
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
        $this->setName('lom:au:content');
        $this->setDescription('List the content of an AU in a lockss box');
        $this->addArgument('au', InputArgument::REQUIRED, 'The LOM database ID of the AU to query.');
        $this->addArgument('box', InputArgument::REQUIRED, 'The LOM database ID of the box to query.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function listAu(Au $au, Box $box, OutputInterface $output)
    {
        $auid = $this->idGenerator->fromAu($au);
        $pln = $box->getPln();
        try {
            $url = "http://{$box->getIpAddress()}:{$box->getWebServicePort()}/ws/DaemonStatusService?wsdl";
            $statusClient = new SoapClient($url, array(
                'soap_version' => SOAP_1_1,
                'login' => $pln->getUsername(),
                'password' => $pln->getPassword(),
                'trace' => true,
                'exceptions' => true,
                'cache' => WSDL_CACHE_NONE,
            ));
            $statusResponse = $statusClient->getAuUrls(array(
                'auId' => $auid,
            ));
            print_r($statusResponse);
        } catch (Exception $e) {
            $this->logger->warning($box->getHostname().'/'.$box->getIpAddress().' - '.$e->getMessage());
        }
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $au = $this->em->find('LOCKSSOMaticCrudBundle:Au', $input->getArgument('au'));
        $box = $this->em->find('LOCKSSOMaticCrudBundle:Box', $input->getArgument('box'));
        $this->listAu($au, $box, $output);
    }
}
