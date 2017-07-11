<?php

namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Show the checksums of a deposit in the LOCKSS boxes.
 */
class DepositDetailsCommand extends ContainerAwareCommand
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

    /**
     * {@inheritDoc}
     */
    public function configure() {
        $this->setName('lom:deposit:details');
        $this->setDescription('Check that the deposits in LOCKSS have the same checksum.');
        $this->addArgument('depositId', InputArgument::REQUIRED, 'The database ID of the deposit.');
    }

    /**
     * {@inheritDoc}
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->logger = $container->get('logger');
        $this->em = $container->get('doctrine')->getManager();
        $this->idGenerator = $this->getContainer()->get('crud.au.idgenerator');
    }

    /**
     * Get the checksum of a content item in a lockss box via SOAP calls.
     *
     * @param Box $box
     * @param Content $content
     * @return string|array
     */
    protected function getBoxChecksum(Box $box, Content $content) {
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

            return;
        }
        if (property_exists($response->return, 'blockFileDataHandler')) {
            $matches = array();
            if (preg_match("/^([a-fA-F0-9]+)\s+http/m", $response->return->blockFileDataHandler, $matches)) {
                return $matches[1];
            } else {
                return '-';
            }
        } else {
            return $response->return->errorMessage;
        }
    }

    /**
     * Get the deposits to check based on database IDs.
     *
     * @param int $id database ID.
     *
     * @return Deposit
     */
    protected function getDeposit($id) {
        $deposit = $this->em->find('LOCKSSOMaticCrudBundle:Deposit', $id);

        return $deposit;
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $id = $input->getArgument('depositId');
        $deposit = $this->getDeposit($id);
        if (!$deposit) {
            $output->writeln('No deposit with that ID found.');
            exit;
        }
        foreach ($deposit->getContent() as $content) {
            $output->writeln($content->getUrl());
            $output->writeln("{$content->getChecksumValue()}:{$content->getChecksumType()}");

            foreach ($deposit->getPln()->getActiveBoxes() as $box) {
                $checksum = $this->getBoxChecksum($box, $content);
                $output->writeln("{$checksum}:{$box->getHostname()}");
            }
        }
    }
}
