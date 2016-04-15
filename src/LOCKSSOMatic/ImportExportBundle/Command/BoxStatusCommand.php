<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

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

class BoxStatusCommand extends ContainerAwareCommand {

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
	 * @var AuIdGenerator
	 */
	private $idGenerator;

	public function configure() {
		$this->setName('lom:box:status');
		$this->setDescription('Check the status of the LOCKSS AUs');
		$this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
	}

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->logger = $container->get('logger');
		$this->em = $container->get('doctrine')->getManager();
		$this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
	}

	protected function loadBoxes(Pln $pln) {
		$boxes = $pln->getBoxes();
		foreach ($boxes as $box) {
			$statusClient = new SoapClient("http://{$box->getIpAddress()}:8081/ws/DaemonStatusService?wsdl", array(
				'soap_version' => SOAP_1_1,
				'login' => 'lockss-u',
				'password' => 'lockss-p',
				'trace' => true,
				'exceptions' => true,
				'cache' => WSDL_CACHE_NONE,
			));
			$readyResponse = $statusClient->isDaemonReady();
			if ($readyResponse) {
				$this->boxes[] = $box;
			} else {
				$this->logger->error("Box {$box->getId()} is not ready.");
			}
		}
	}

	protected function checkBox(Box $box) {
		$statusClient = new SoapClient("http://{$box->getIpAddress()}:8081/ws/DaemonStatusService?wsdl", array(
			'soap_version' => SOAP_1_1,
			'login' => 'lockss-u',
			'password' => 'lockss-p',
			'trace' => true,
			'exceptions' => true,
			'cache' => WSDL_CACHE_NONE,
		));
		try {
			$spacesResponse = $statusClient->queryRepositorySpaces(array('repositorySpaceQuery' => 'SELECT *'));
			return get_object_vars($spacesResponse->return);
		} catch (Exception $e) {
			print $e->getMessage();
		}
	}

	/**
	 * @return Pln
	 * @param array|null $plnIds
	 */
	protected function getPlns($plnIds = null) {
		$pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find(1);
		return $pln;
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$pln = $this->getPlns();
		$this->loadBoxes($pln);
		foreach ($pln->getBoxes() as $box) {
			$status = $this->checkBox($box);
			$boxStatus = new BoxStatus();
			$boxStatus->setBox($box);
			$boxStatus->setQueryDate(new DateTime());
			$boxStatus->setStatus($status);
			if($input->getOption('dry-run')) {
				continue;
			}
			$this->em->persist($boxStatus);
			$this->em->flush();
		}
	}

}
