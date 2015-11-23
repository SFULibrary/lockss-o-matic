<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Entity\PlnProperty;
use LOCKSSOMatic\ImportExportBundle\Services\PLNPluginImportService;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network plugin import command-line
 */
class PLNImportCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Logger
     */
    private $logger;


    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
    }

    public function configure()
    {
        $this->setName('lom:import:pln')
            ->setDescription('Import PLN XML file.')
            ->addArgument(
                'id',
                null,
                InputArgument::REQUIRED,
                "LOCKSSOMatic's ID for the PLN"
            )
            ->addArgument(
                'file',
                null,
                InputArgument::REQUIRED,
                'Local file path to the lockss.xml file'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();

        /** @var Pln $pln */
        $id = $input->getArgument('id');
        $pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);
        if ($pln === null) {
            $this->logger->critical("Cannot find PLN {$id}");
            exit;
        }
        $this->output = $output;

        $xml = simplexml_load_file($input->getArgument('file'));
        $root = $xml->xpath('/lockss-config/property');
        $this->importProperties($pln, $root[0]);        
        $this->em->refresh($pln);
        $this->importBoxes($pln);

        $this->em->flush();
        $activityLog->enable();
    }
    
    private function importBoxes(Pln $pln) {
        $boxDefs = $pln->getProperty('id.initialV3PeerList');
        foreach ($boxDefs->getPropertyValue() as $def) {
            $matches = array();
            preg_match('/^(\w+):\[(\d+\.\d+\.\d+\.\d+)\]:(\d+)$/', $def, $matches);
            $box = new Box();
            $box->setProtocol($matches[1]);
            $box->setIpAddress($matches[2]);
            $box->setPort($matches[3]);
            $box->setPln($pln);
            $box->setHostname(gethostbyaddr($box->getIpAddress()));
            $this->em->persist($box);
        }
    }

    private function getList(SimpleXMLElement $node)
    {
        $valueNodes = $node->xpath('value');
        $values = array();
        foreach ($valueNodes as $n) {
            $values[] = (string)$n;
        }
        return $values;
    }

    private function importProperties(Pln $pln, SimpleXMLElement $node, PlnProperty $parent = null)
    {
        $property = new PlnProperty();
        $property->setPropertyKey($node['name']);
        $property->setParent($parent);
        $property->setPln($pln);
        $property->setPropertyValue($node['value']);
        $this->em->persist($property);

        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'property':
                    $this->importProperties($pln, $child, $property);
                    break;
                case 'list':
                    $property->setPropertyValue($this->getList($child));
                    break;
                default:
                    $this->logger->warning("(probably harmless): Unknown node name: {$child->getName()}");
            }
        }
    }
}
