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
     * @var AuIdGenerator
     */
    private $idGenerator;

    private $SKIP_BOXES = array(3, 7);

    public function configure()
    {
        $this->setName('lom:deposit:status');
        $this->setDescription('Check that the deposits in LOCKSS have the same checksum.');
        $this->addOption('all', '-a', InputOption::VALUE_NONE, 'Process all deposits.');
        $this->addOption('pln', null, InputOption::VALUE_REQUIRED, 'Optional list of PLNs to check.');
        $this->addOption('limit', '-l', InputOption::VALUE_REQUIRED, 'Limit the number of deposits checked.');
        $this->addOption('dry-run', '-d', InputOption::VALUE_NONE, 'Export only, do not update any internal configs.');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    protected function getBoxChecksum(Box $box, Content $content)
    {
        $auid = $this->idGenerator->fromContent($content);
        $checksumType = $content->getChecksumType();

        $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/HasherService?wsdl";
        $client = new LockssSoapClient();
        $client->setWsdl($wsdl);
        $client->setOption('login', $box->getPln()->getUsername());
        $client->setOption('password', $box->getPln()->getPassword());

        $response = $client->call('hash', array(
            'hasherParams' => array(
                'recordFilterStream' => true,
                'hashType' => 'V3File',
                'algorithm' => $checksumType,
                'url' => $content->getUrl(),
                'auId' => $auid,
        ), ));
        if ($response === null) {
            $this->logger->warning("{$wsdl} failed.");
            $this->logger->warning($client->getErrors());

            return '*';
        }
        if (property_exists($response->return, 'blockFileDataHandler')) {
            $matches = array();
            if (preg_match("/^([a-fA-F0-9]+)\s+http/m", $response->return->blockFileDataHandler, $matches)) {
                return $matches[1];
            } else {
                return '-';
            }
        } else {
            $this->logger->warning("{$wsdl} returned error.");
            $this->logger->warning($response->return->errorMessage);

            return '*';
        }
    }

    /**
     * @param type $all
     * @param type $limit
     *
     * @return Deposit[]
     */
    protected function getDeposits($all, $limit, $plnId)
    {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        $qb = $repo->createQueryBuilder('d');
        if (!$all) {
            $qb->where('d.agreement <> 1');
            $qb->orWhere('d.agreement is null');
        }
        if ($plnId !== null) {
            $plns = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findOneBy(array('id' => $plnId));
            $qb->innerJoin('d.contentProvider', 'p', 'WITH', 'p.pln = :pln');
            $qb->setParameter('pln', $plns);
        }
        $qb->orderBy('d.id');
        $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    protected function queryDeposit(Deposit $deposit)
    {
        $pln = $deposit->getPln();
        $boxes = $pln->getBoxes();
        $contents = $deposit->getContent();

        $total = count($boxes) * count($contents); // total number of checksums needed to match.
        $matches = 0;
        $result = array();
        $agreement = 0;

        foreach ($contents as $content) {
            $result[$content->getId()] = array();
            $result[$content->getId()]['expected'] = $content->getChecksumValue();
            foreach ($boxes as $box) {
                if (in_array($box->getId(), $this->SKIP_BOXES)) {
                    $result[$content->getId()][$box->getHostname()] = '*';
                    continue;
                }
                $checksum = $this->getBoxChecksum($box, $content);
                if (strtoupper($content->getChecksumValue()) === strtoupper($checksum)) {
                    ++$matches;
                }
                $result[$content->getId()][$box->getHostname()] = $checksum;
            }
        }
        if ($matches === $total) {
            $agreement = 1; // avoid rounding issues.
        } else {
            $agreement = $matches / $total;
        }

        return array($agreement, $result);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $all = $input->getOption('all');
        $plnId = $input->getOption('pln');
        $dryRun = $input->getOption('dry-run');
        $limit = $input->getOption('limit');

        $deposits = $this->getDeposits($all, $limit, $plnId);
        $this->logger->notice('Checking deposit status for '.count($deposits).' deposits.');

        foreach ($deposits as $deposit) {
            $result = $this->queryDeposit($deposit);
            $this->logger->notice("{$deposit->getPln()->getId()} - {$result[0]} - {$deposit->getUUid()}");
            if ($dryRun) {
                continue;
            }

            $deposit->setAgreement($result[0]);
            $status = new DepositStatus();
            $status->setDeposit($deposit);
            $status->setQueryDate(new DateTime());
            $status->setAgreement($result[0]);
            $status->setStatus($result[1]);

            $this->em->persist($status);
            $this->em->flush();
        }
    }
}
