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

/**
 * Symfony command to list all of the AUs in a PLN. Uses a SOAP call
 * to get the list of AUs.
 */
class ListAusCommand extends ContainerAwareCommand
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
     * {@inheritDocs}
     */
    public function configure() {
        $this->setName('lom:au:list');
        $this->setDescription('List the AUs on one box.');
        $this->addArgument('box', InputArgument::REQUIRED, 'The LOM database ID of the box to query.');
    }

    /**
     * {@inheritdoc}
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
     * Contact the box and list its AUs.
     *
     * @todo don't use print_r for this.
     *
     * @param Box $box
     * @param OutputInterface $output
     */
    protected function listAus(Box $box, OutputInterface $output) {
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
            $statusResponse = $statusClient->getAuIds();
            foreach($statusResponse->return as $id => $obj) {
                $output->writeln("\n" . $id . ":" . $obj->name);
                $output->writeln($obj->id);
            }
        } catch (Exception $e) {
            $this->logger->warning($box->getHostname().'/'.$box->getIpAddress().' - '.$e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $box = $this->em->find('LOCKSSOMaticCrudBundle:Box', $input->getArgument('box'));
        $this->listAus($box, $output);
    }
}
