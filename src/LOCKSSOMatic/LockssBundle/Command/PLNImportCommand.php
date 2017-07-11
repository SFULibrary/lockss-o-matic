<?php


namespace LOCKSSOMatic\LockssBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network import command. Imports a lockss.xml configuration
 * file. You can give it a file path (/path/to/file) or a URL.
 */
class PLNImportCommand extends ContainerAwareCommand
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
     * {@inheritDoc}
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->logger = $container->get('logger');
    }

    /**
     * {@inheritDoc}
     */
    public function configure() {
        $this->setName('lom:import:pln')->setDescription('Import PLN XML file.')->addArgument(
            'id',
            null,
            InputArgument::REQUIRED,
            "LOCKSSOMatic's ID for the PLN"
        )->addArgument(
            'file',
            null,
            InputArgument::REQUIRED,
            'Local file path to the lockss.xml file'
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();

        $id = $input->getArgument('id');
        $pln = $this->em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);
        if ($pln === null) {
            throw new Exception("Cannot find pln {$id}");
        }

        $xml = simplexml_load_file($input->getArgument('file'));
        $this->importProperties($pln, $xml);

        $this->em->flush();
        $activityLog->enable();
    }

    /**
     * Import the XML properties for a PLN.
     *
     * @param Pln $pln
     * @param SimpleXMLElement $xml
     * @param string $prefix
     */
    public function importProperties(Pln $pln, SimpleXMLElement $xml, $prefix = '') {
        foreach ($xml->children() as $node) {
            switch ($node->getName()) {
                case 'property':
                    $name = $node['name'];
                    if ($node['value']) {
                        $pln->setProperty("{$prefix}{$name}", (string) $node['value']);
                    } else {
                        $this->importProperties($pln, $node, "{$prefix}{$name}.");
                    }
                    break;
                case 'list':
                    $v = array();
                    foreach ($node->children() as $value) {
                        $v[] = (string) $value;
                    }
                    $pln->setProperty(rtrim($prefix, '.'), $v);
                    break;
                default:
                    $this->importProperties($pln, $node, $prefix);
            }
        }
    }
}
