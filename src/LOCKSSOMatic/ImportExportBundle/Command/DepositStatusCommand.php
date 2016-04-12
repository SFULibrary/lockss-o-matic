<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use SoapClient;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DepositStatusCommand extends ContainerAwareCommand {

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

	public function configure() {
		$this->setName('lom:deposit:status');
		$this->setDescription('Check that the deposits in LOCKSS have the same checksum.');
		$this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
		$this->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Only process $limit deposits.');
	}

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->logger = $container->get('logger');
		$this->em = $container->get('doctrine')->getManager();
	}

	protected function loadBoxes(Pln $pln) {
		$boxes = $pln->getBoxes();
		$this->boxCount = count($boxes);
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
			if($readyResponse) {
				$this->boxes[] = $box;
			} else {
				$this->logger->error("Box {$box->getId()} is not ready.");
			}
		}
		$this->logger->info("Checking content status on " . count($this->boxes) . " of {$this->boxCount}");
	}

	protected function checkContent(Content $content) {
		$this->logger->info("Checking content {$content->getId()}");
		$auid = $content->getAu()->getAuid();
		$checksumValue = $content->getChecksumValue();
		$checksumType = $content->getChecksumType();

		$matches = 0;
		foreach ($this->boxes as $box) {
			$this->logger->info("Checking content on box {$box->getId()}");
			$hasherClient = new SoapClient('http://localhost:8081/ws/HasherService?wsdl', array(
				'soap_version' => SOAP_1_1,
				'login' => $box->getUsername(),
				'password' => $box->getPassword(),
				'trace' => true,
				'exceptions' => true,
				'cache' => WSDL_CACHE_NONE,
			));
			$hashResponse = $hasherClient->hash(array(
				'hasherParams' => array(
					'recordFilterStream' => true,
					'hashType' => 'V3File',
					'algorithm' => $checksumType,
					'url' => $content->getUrl(),
					'auId' => $auid,
			)));
			if (property_exists($hashResponse->return, 'blockFileDataHandler')) {
				$matches = array();
				if (preg_match("/^([a-fA-F0-9]+)\s+http/m", $hashReq->return->blockFileDataHandler, $matches)) {
					print $matches[1] . "\n";
				}
			} else {
				$this->logger->info("Error: " . print_r($hashResponse->return));
			}
		}
	}

	protected function checkDeposit(Deposit $deposit) {
		$this->logger->info("Checking deposit {$deposit->getId()}");
		$matches = 0;
		foreach ($deposit->getContent() as $content) {
			$matches += $this->checkContent($content);
		}
		return $matches / (count($deposit->getContent()) * count($this->boxes));
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
		foreach($pln->getContentProviders() as $provider) {
			foreach($provider->getDeposits() as $deposit) {
				$this->checkDeposit($deposit);
			}
		}
	}
}
