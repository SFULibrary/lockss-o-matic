<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;
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
class PLNTitledbImportCommand extends ContainerAwareCommand
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
            ->addArgument('titledbs', InputArgument::IS_ARRAY,
                'Local path to the titledb xml file.');
    }

    /**
     * @return Logger
     */
    protected function getLogger() {
        return $this->getContainer()->get('logger');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $titleFiles= $input->getArgument('titledbs');
        $logger = $this->getLogger();

        foreach($titleFiles as $file) {
            $logger->notice("Importing titles from {$file}");
            if( ! file_exists($file)) {
                $logger->critical("Cannot find {$file}");
                continue;
            }
            $this->processFile($file);
        }
    }

    protected function processFile($file) {
        $logger = $this->getLogger();
        $xml = simplexml_load_file($file);
        $titles = $xml->xpath('//lockss-config/property[@name="org.lockss.title"]/property');
        $count = count($titles);
        $logger->notice("Found $count AU stanzas.");

        $i = 0;
        foreach($titles as $title) {
            try {
                $this->processTitle($title);
                $i++;
                if($i % 200 === 0) {
                    $this->reportProgress($i, $count);
                }
            } catch (Exception $e) {
                $logger->error("Import error: {$e->getMessage()}");
                if(($p = $e->getPrevious()) !== null) {
                    $logger->error($p->getMessage());
                }
            }
        }
        $this->reportProgress($i, $count);

    }

    protected function reportProgress($processed, $total) {
        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
        $this->getLogger()->notice("{$processed} of {$total}");
    }

    protected function processTitle(SimpleXMLElement $title) {
        $au = $this->buildAu($title);
        foreach($au->getAuProperties() as $property) {
            Debug::dump($property);
            $this->em->persist($property);
        }
        $this->em->persist($au);
        $this->em->flush();
        Debug::dump($au);
    }

    protected function buildAu(SimpleXMLElement $title) {
        $au = new Au();
        $au->setComment('AU created by import command a.');
        $au->setPlugin($this->getPlugin($title));

        $root = new AuProperty();
        $root->setPropertyKey((string)$title->attributes()->name);
        $root->setAu($au);
        $this->findChildProperties($title, $root);
        print $au->generateAuid();
        return $au;
    }

    public function findChildProperties(SimpleXMLElement $xml, AuProperty $parent = null) {
        foreach($xml->xpath('property') as $x) {
            $child = new AuProperty();
            $child->setPropertyKey((string)$x->attributes()->name);
            $child->setPropertyValue((string)$x->attributes()->value);
            $child->setParent($parent);
            $child->setAu($parent->getAu());
            $this->findChildProperties($x, $child);
        }
    }

    protected function getPropertyValue(SimpleXMLElement $xml, $name) {
        $nodes = $xml->xpath("property[@name='{$name}']/@value");
        if(count($nodes) === 0) {
            return null;
        }
        if(count($nodes) === 1) {
            return (string) $nodes[0];
        }
        throw new Exception("Too many elements for property {$name}");
    }

    protected function getPlugin(SimpleXMLElement $xml) {
        // cache the plugins for speed.
        static $pluginCache = array();

        $pluginId = $this->getPropertyValue($xml, 'plugin');
        if($pluginId === null) {
            throw new Exception("AU stanza does not have a plugin property.");
        }
        if(array_key_exists($pluginId, $pluginCache) && $this->em->contains($pluginCache[$pluginId])) {
            return $pluginCache[$pluginId];
        }
        $property = $this->em->getRepository('LOCKSSOMaticCrudBundle:PluginProperty')
            ->findOneBy(array(
                'propertyKey' => 'plugin_identifier',
                'propertyValue' => $pluginId
            ));
        if($property === null) {
            throw new Exception("Unknown pluginId: {$pluginId}");
        }
        $pluginCache[$pluginId] = $property->getPlugin();
        return $pluginCache[$pluginId];
    }

    public function getContentOwner($name) {
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
