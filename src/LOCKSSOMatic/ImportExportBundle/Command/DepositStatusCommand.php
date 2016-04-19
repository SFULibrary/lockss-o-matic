<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
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

	/**
	 * @var AuIdGenerator
	 */
	private $idGenerator;

	public function configure() {
		$this->setName('lom:deposit:status');
		$this->setDescription('Check that the deposits in LOCKSS have the same checksum.');
        $this->addArgument('plns', InputArgument::IS_ARRAY, 'Database IDs of the PLNs to check.');
        $this->addOption('all', '-a', InputOption::VALUE_NONE, 'Process all deposits.');
		$this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
        $this->addOption('no-clean', null, InputOption::VALUE_NONE, 'Do not remove deposits that have 100% agreement.');
	}

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->logger = $container->get('logger');
		$this->em = $container->get('doctrine')->getManager();
		$this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
	}

	protected function loadBoxes(Pln $pln) {
		$boxes = $pln->getBoxes();
        $this->boxes = array();
		$this->boxCount = count($boxes);
		foreach ($boxes as $box) {
			try {
				$statusClient = new SoapClient("http://{$box->getIpAddress()}:8081/ws/DaemonStatusService?wsdl", array(
					'soap_version' => SOAP_1_1,
					'login' => 'lockss-u',
					'password' => 'lockss-p',
					'trace' => false,
					'exceptions' => true,
					'cache' => WSDL_CACHE_NONE,
				));
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

	protected function checkContent(Content $content) {
		$this->logger->info("Checking content #{$content->getId()}");
		$auid = $this->idGenerator->fromContent($content);
		$checksumValue = $content->getChecksumValue();
		$checksumType = $content->getChecksumType();

        $status = 'agreement';
		$checksumMatches = 0;
        
		foreach ($this->boxes as $box) {
			$url = "http://{$box->getIpAddress()}:{$box->getWebServicePort()}/ws/HasherService?wsdl";
			try {
				$hasherClient = new SoapClient($url, array(
					'soap_version' => SOAP_1_1,
					'login' => $box->getUsername(),
					'password' => $box->getPassword(),
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
					if (preg_match("/^([a-fA-F0-9]+)\s+http/m", $hashResponse->return->blockFileDataHandler, $matches)) {
						$checksumValue = $matches[1];
						if (strtoupper($checksumValue) === strtoupper($content->getChecksumValue())) {
							$checksumMatches++;
						} else {
							$this->logger->warning("  box {$box->getId()} Checksum mismatch. Expected {$content->getChecksumValue()} Got {$checksumValue}");
						}
					}
				} else {
					$this->logger->warning("Error from {$url}: {$hashResponse->return->errorMessage}");
				}
			} catch (Exception $e) {
				$this->logger->error($box->getHostname() . '/' . $box->getIpAddress() . ' - ' . $e->getMessage());
			}
		}
		return $checksumMatches;
	}

	protected function checkDeposit(Deposit $deposit) {
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
        if($plnIds === null || !is_array($plnIds) || count($plnIds) === 0) {
            return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
        }
		return $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findBy(
            array('id' => $plnIds)
        );
	}
    
    protected function processDeposit(Deposit $deposit, $dryRun) {
        $agreement = $this->checkDeposit($deposit);
        $deposit->setAgreement($agreement);
        if ($dryRun) {
            return;
        }
        $this->em->flush();
    }
    
    protected function processPln(Pln $pln, $allDeposits, $dryRun) {
		$this->loadBoxes($pln);        
		if (count($this->boxes) === 0) {
            $this->logger->critical("No boxes available to check deposit status in {$pln->getName()}");
			return;
		}
        
		foreach ($pln->getContentProviders() as $provider) {
			foreach ($provider->getDeposits() as $deposit) {
				if ($deposit->getAgreement() == 1 && (! $allDeposits)) {
					continue;
				}
                $this->processDeposit($deposit, $dryRun);
			}
		}
        
    }

	public function execute(InputInterface $input, OutputInterface $output) {
        $allDeposits = $input->getOption('all');
        $dryRun = $input->getOption('dry-run');
		foreach($this->getPlns($input->getArgument('plns')) as $pln) {
            $this->processPln($pln, $allDeposits, $dryRun);
    	}
    }
}
