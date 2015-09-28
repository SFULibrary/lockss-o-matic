<?php

namespace LOCKSSOMatic\ImportExportBundle\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
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
            ->addArgument('path_to_titledb', InputArgument::REQUIRED,
                'Local path to the titledb xml file.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $pathToTitledb = $input->getArgument('path_to_titledb');
        if (!file_exists($pathToTitledb)) {
            $output->writeln("Cannot find {$pathToTitledb}");
            return;
        }

        $xml = simplexml_load_file($pathToTitledb);
        $titlesXml = $xml->xpath('//lockss-config/property[@name="org.lockss.title"]/property');
        $total = count($titlesXml);
        $output->writeln("Found {$total} title elements.");
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $errors = array();
        $i = 0;
        foreach ($titlesXml as $titleXml) {
            $result = $this->processTitle($titleXml);
            if($result !== null) {
                $errors[$result] = (array_key_exists($result, $errors) ? $errors[$result] + 1 : 1);
            }
            $i++;
            if ($i % 200 === 0) {
                $this->progressReport($output, $total, $i);
            }
        }
        $this->progressReport($output, $total, $total);
        if (count($errors) > 0) {
            foreach ($errors as $k => $v) {
                $output->writeln("Error ($v) $k");
            }
        }
    }

    public function progressReport(OutputInterface $output, $total, $i) {
        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
        $output->writeln(" $i / $total - " . sprintf('%dM', memory_get_usage() / (1024 * 1024)) . '/' . ini_get('memory_limit'));
    }
    
    public function processTitle(SimpleXMLElement $titleXml)
    {
        try {
            $this->addAu($titleXml);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function getPropertyValue(SimpleXMLElement $xml, $name)
    {
        $dataNodes = $xml->xpath("property[@name='{$name}']/@value");
        if (count($dataNodes) === 0) {
            return null;
        }
        if (count($dataNodes) === 1) {
            return (string) $dataNodes[0];
        }
        throw new Exception('Too many elements for property name ' . $name);
    }

    /**
     * Get a plugin based on its identifier property.
     *
     * @todo some caching here would be very good.
     *
     * @param string $pluginId
     *
     * @return Plugin
     */
    public function getPlugin($pluginId)
    {
        static $cache = array();
        // $this->em->clear() may disconnect entities in this cache
        // for some reason.
        if (array_key_exists($pluginId, $cache) && $this->em->contains($cache[$pluginId])) {
            return $cache[$pluginId];
        }

        $property = $this->em->getRepository('LOCKSSOMaticCrudBundle:PluginProperty')
            ->findOneBy(array(
            'propertyKey'   => 'plugin_identifier',
            'propertyValue' => $pluginId
        ));

        if ($property === null) {
            throw new Exception("Unknown pluginId property: {$pluginId}");
        }

        $cache[$pluginId] = $property->getPlugin();
        return $property->getPlugin();
    }

    public function getContentOwner($name, Plugin $plugin)
    {
        static $cache = array();

        if (array_key_exists($name, $cache)) {
            return $cache[$name];
        }

        $owner = $this->em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')
            ->findOneBy(array(
            'name' => $name
        ));
        if ($owner === null) {
            $owner = new ContentOwner();
            $owner->setName($name);
            $owner->setEmailAddress('unknown');
            $this->em->persist($owner);
        }
        $cache[$name] = $owner;
        return $cache[$name];
    }

    public function findChildProperties(SimpleXMLElement $xml, AuProperty $parent = null) {
        foreach($xml->xpath('property') as $x) {
            $child = new AuProperty();
            $child->setPropertyKey($x->attributes()->name);
            $child->setPropertyValue($x->attributes()->value);
            $child->setParent($parent);
            $child->setAu($parent->getAu());
            $this->em->persist($child);
            $this->findChildProperties($x, $child);
        }
    }

    public function addAu(SimpleXMLElement $xml)
    {
        $pluginId = $this->getPropertyValue($xml, 'plugin');
        $plugin = $this->getPlugin($pluginId);
        $publisherName = (string) $this->getPropertyValue($xml, 'attributes.publisher');
        $this->getContentOwner($publisherName, $plugin);

        $au = new Au();
        $au->setComment('AU created by import command');
        $plugin->addAus($au);
        $au->setPlugin($plugin);
        $this->em->persist($au);

        $prop = new AuProperty();
        $prop->setPropertyKey((string)$xml->attributes()->name);
        $prop->setAu($au);
        $this->em->persist($prop);
        $this->findChildProperties($xml, $prop);
    }


}
