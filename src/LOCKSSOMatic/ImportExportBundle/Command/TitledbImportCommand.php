<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Private Lockss network plugin import command-line
 */
class TitledbImportCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    public function configure()
    {
        $this->setName('lom:import:titledb')
            ->setDescription('Import PLN titledb file.')
            ->addArgument(
                'providerId',
                InputArgument::REQUIRED,
                'ID of the content provider for the titles.'
            )
            ->addArgument(
                'titledbs',
                InputArgument::IS_ARRAY,
                'Local path(s) to the titledb xml file.'
            );
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $titleFiles = $input->getArgument('titledbs');
        $provider = $this->em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($input->getArgument('providerId'));
        
        $logger = $this->getLogger();

        foreach ($titleFiles as $file) {
            $logger->notice("Importing titles from {$file}");
            if (!file_exists($file)) {
                $logger->critical("Cannot find {$file}");
                continue;
            }
            $this->processFile($file, $output, $provider);
        }
    }

    protected function processFile($file, OutputInterface $output, ContentProvider $provider)
    {
        $xml = simplexml_load_file($file);
        $titles = $xml->xpath('//lockss-config/property[@name="org.lockss.title"]/property');
        $count = count($titles);
        $output->writeln("Found $count AU stanzas.");

        $i = 0;
        foreach ($titles as $title) {
            try {
                $this->processTitle($title, $provider);
            } catch (Exception $e) {
                $output->writeln("Import error: {$e->getMessage()}");
                if (($p = $e->getPrevious()) !== null) {
                    $output->writeln($p->getMessage());
                }
            }
            $i++;
            if ($i % 200 === 0) {
                $this->reportProgress($i, $count, $output);
            }
        }
        $this->reportProgress($i, $count, $output);
    }

    protected function reportProgress($processed, $total, $output)
    {
        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
        $memory = sprintf('%dM', memory_get_usage() / (1024 * 1024));
        $available = ini_get('memory_limit');

        $output->writeln(" {$processed} / {$total} - {$memory} of {$available}");
    }

    protected function processTitle(SimpleXMLElement $title, ContentProvider $provider)
    {
        $au = $this->buildAu($title);
        $au->setContentprovider($provider);
        $au->setPln($provider->getPln());
        foreach ($au->getAuProperties() as $property) {
            $this->em->persist($property);
        }
        $this->em->persist($au);
    }

    public function buildAu(SimpleXMLElement $title)
    {
        $au = new Au();
        $au->setComment('AU created by import command a.');
        $au->setPlugin($this->getPlugin($title));

        $root = new AuProperty();
        $root->setPropertyKey((string) $title->attributes()->name);
        $root->setAu($au);
        $this->buildChildProperties($title, $root);
        return $au;
    }

    public function buildChildProperties(SimpleXMLElement $xml, AuProperty $parent = null)
    {
        foreach ($xml->xpath('property') as $x) {
            $child = new AuProperty();
            $child->setPropertyKey((string) $x->attributes()->name);
            $child->setPropertyValue((string) $x->attributes()->value);
            $child->setParent($parent);
            $child->setAu($parent->getAu());
            $this->buildChildProperties($x, $child);
        }
    }

    public function getPropertyValue(SimpleXMLElement $xml, $name)
    {
        $nodes = $xml->xpath("property[@name='{$name}']/@value");
        if (count($nodes) === 0) {
            return null;
        }
        if (count($nodes) === 1) {
            return (string) $nodes[0];
        }
        throw new Exception("Too many elements for property {$name}");
    }

    public function getPlugin(SimpleXMLElement $xml)
    {
        // cache the plugins for speed.
        static $pluginCache = array();

        $pluginId = $this->getPropertyValue($xml, 'plugin');
        if ($pluginId === null) {
            throw new Exception("AU stanza does not have a plugin property.");
        }
        if (array_key_exists($pluginId, $pluginCache) && $this->em->contains($pluginCache[$pluginId])) {
            return $pluginCache[$pluginId];
        }
        $pluginRepo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Plugin');
        $pluginList = $pluginRepo->findByPluginIdentifier($pluginId);
        $plugin = $pluginList[0];
        if ($plugin === null) {
            throw new Exception("Unknown pluginId: {$pluginId}");
        }
        $pluginCache[$pluginId] = $plugin;
        return $pluginCache[$pluginId];
    }

    public function getContentOwner($name)
    {
        static $ownerCache = array();

        if (array_key_exists($name, $ownerCache) && $this->em->contains($ownerCache[$name])) {
            return $ownerCache[$name];
        }

        $owner = $this->em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')
            ->findOneBy(array(
            'name' => $name
            ));
        if ($owner === null) {
            $owner = new ContentOwner();
            $owner->setName($name);
            $this->em->persist($owner);
        }
        $ownerCache[$name] = $owner;
        return $ownerCache[$name];
    }
}
