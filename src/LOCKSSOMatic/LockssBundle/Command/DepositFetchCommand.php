<?php


namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\Services\FilePaths;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Services\ContentFetcherService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fetch a deposit's content items from the PLN. Verifies the checksum on the box
 * before downloading, then verifies it again after download.
 */
class DepositFetchCommand extends ContainerAwareCommand
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
     * @var ContentFetcherService
     */
    private $fetcher;
    
    /**
     * @var FilePaths
     */
    private $filePaths;

    /**
     * {@inheritDoc}
     */
    public function configure() {
        $this->setName('lom:deposit:fetch');
        $this->setDescription('Fetch one or more deposits from the PLN.');
        $this->addArgument('uuids', InputArgument::IS_ARRAY, 'One or more deposit UUIDs to fetch.');
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
        $this->idGenerator = $container->get('crud.au.idgenerator');
        $this->fetcher = $container->get('lockss.content.fetcher');
        $this->filePaths = $container->get('lom.filepaths');
    }

    /**
     * Gets the boxes for a PLN in a random order.
     *
     * @param Pln $pln
     *
     * @return Box[]
     */
    public function loadBoxes(Pln $pln) {
        $boxes = $pln->getActiveBoxes()->toArray();
        shuffle($boxes);
        return $boxes;
    }

    /**
     * Get the deposits to download.
     *
     * @param string[] $uuids
     * @return Deposit[]|Collection
     */
    protected function getDeposits($uuids) {
        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        return $repo->findBy(array('uuid' => $uuids));
    }

    /**
     * Download a deposit from the network.
     *
     * @param Deposit $deposit
     */
    protected function fetchDeposit(Deposit $deposit) {
        foreach($deposit->getContent() as $content) {
            $file = $this->fetcher->fetch($content);
            if( ! $file) {
                return;
            }
            $path = $this->filePaths->getDownloadContentPath($content);
            $dir = pathinfo($path, PATHINFO_DIRNAME);
            if( !file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fh = fopen($path, 'wb');
            while($data = fread($file, 1024*64)) {
                fwrite($fh, $data);
            }
            $this->logger->notice("wrote to " . $path);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $uuids = $input->getArgument('uuids');
        $deposits = $this->getDeposits($uuids);
        $this->logger->notice("Fetching " . count($deposits) . " deposit(s)");

        foreach($deposits as $deposit) {
            $this->logger->notice("Fetching {$deposit->getUuid()}");
            $this->fetchDeposit($deposit);
        }
    }
}
