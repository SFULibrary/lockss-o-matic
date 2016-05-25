<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuStatus;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuStatusCommand extends ContainerAwareCommand {

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

	public function configure() {
		$this->setName('lom:au:status');
		$this->setDescription('Check the status of the LOCKSS AUs');
        $this->addArgument('aus', InputArgument::IS_ARRAY, "Optional list of AU database Iids to check.");
		$this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
	}

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->logger = $container->get('logger');
		$this->em = $container->get('doctrine')->getManager();
		$this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
	}
    

	protected function checkAu(Au $au) {
		$auid = $this->idGenerator->fromAu($au);
        $pln = $au->getPln();
        $boxes = $pln->getBoxes();
		$statuses = array();
        $errors = array();
		foreach ($boxes as $box) {
            $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/DaemonStatusService?wsdl";
            $this->logger->notice("checking {$wsdl}");
            $client = new LockssSoapClient();
            $client->setWsdl($wsdl);
            $client->setOption('login', $pln->getUsername());
            $client->setOption('password', $pln->getPassword());
            $status = $client->call('getAuStatus', array(
                'auId' => $auid,
            ));
            if($status === null) {
                $this->logger->warning("{$wsdl} failed.");
                $errors[$box->getHostname()] = $client->getErrors();
            } else {
                $statuses[$box->getHostname()] = get_object_vars($status->return);
            }
		}
		return array($statuses, $errors);
	}

    protected function getAus($auIds) {
        if($auIds === null || count($auIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Au')->findAll();
        } else {
            return $this->em->getRepository('LOCKSSOMaticCrudBUndle:Au')->findById($auIds);
        }
    }
    
	public function execute(InputInterface $input, OutputInterface $output) {
		$aus = $this->getAus($input->getArgument('aus'));
		foreach ($aus as $au) {
            $auStatus = new AuStatus();
            $auStatus->setAu($au);
            $auStatus->setQueryDate(new DateTime());
			list($status, $errors) = $this->checkAu($au);
            $auStatus->setErrors($errors);
            $auStatus->setStatus($status);
            if($input->getOption('dry-run')) {
                continue;
            }
            $this->em->persist($auStatus);
			$this->em->flush();
		}
	}
}
