<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\LockssBundle\Command;

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
 * Private Lockss network plugin import command-line.
 * 
 * @todo is the use case for this still valid?
 */
class TitledbImportCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDocs}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * {@inheritDocs}
     */
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

    /**
     * {@inheritDocs}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $activityLog = $this->getContainer()->get('activity_log');
        $activityLog->disable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $titleFiles = $input->getArgument('titledbs');

        $logger = $this->getLogger();

        foreach ($titleFiles as $file) {
            $logger->notice("Importing titles from {$file}");
            if (!file_exists($file)) {
                $logger->critical("Cannot find {$file}");
                continue;
            }
            $this->processFile($file, $output, $input->getArgument('providerId'));
        }
    }

    /**
     * Process one titledb XML file.
     * 
     * @param string $file
     * @param OutputInterface $output
     * @param string $providerId
     */
    protected function processFile($file, OutputInterface $output, $providerId)
    {
        $xml = simplexml_load_file($file);
        $titles = $xml->xpath('//lockss-config/property[@name="org.lockss.title"]/property');
        $count = count($titles);
        $output->writeln("Found $count AU stanzas.");
        $provider = $this->em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($providerId);

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
            ++$i;
            if ($i % 200 === 0) {
                $this->reportProgress($i, $count, $output);
            }
        }
        $this->reportProgress($i, $count, $output);
    }

    /**
     * Report the progress of the import to the shell.
     * 
     * @param int $processed
     * @param int $total
     * @param OutputInterface $output
     */
    protected function reportProgress($processed, $total, $output)
    {
        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();
        $memory = sprintf('%dM', memory_get_usage() / (1024 * 1024));
        $available = ini_get('memory_limit');

        $output->writeln(" {$processed} / {$total} - {$memory} of {$available}");
    }

    /**
     * Process one title
     * 
     * @param SimpleXMLElement $title
     * @param ContentProvider $provider
     */
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

    /**
     * Build an AU.
     * 
     * @todo This should use AuBuilder.
     * 
     * @param SimpleXMLElement $title
     * @return Au
     */
    public function buildAu(SimpleXMLElement $title)
    {
        $au = new Au();
        $au->setComment('AU created by import command.');
        $au->setPlugin($this->getPlugin($title));

        $root = new AuProperty();
        $root->setPropertyKey((string) $title->attributes()->name);
        $root->setAu($au);
        $this->buildChildProperties($title, $root);

        return $au;
    }

    /**
     * Build child properties of a property.
     * 
     * @param SimpleXMLElement $xml
     * @param AuProperty $parent
     */
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

    /**
     * Find a property value in some XML.
     * 
     * @param SimpleXMLElement $xml
     * @param string $name
     * @return string 
     * @throws Exception
     */
    public function getPropertyValue(SimpleXMLElement $xml, $name)
    {
        $nodes = $xml->xpath("property[@name='{$name}']/@value");
        if (count($nodes) === 0) {
            return;
        }
        if (count($nodes) === 1) {
            return (string) $nodes[0];
        }
        throw new Exception("Too many elements for property {$name}");
    }

    /**
     * Get the plugin for a title. Plugins are cached in the static $pluginCache.
     * 
     * @staticvar array $pluginCache
     * @param SimpleXMLElement $xml
     * @return array
     * @throws Exception
     */
    public function getPlugin(SimpleXMLElement $xml)
    {
        // cache the plugins for speed.
        static $pluginCache = array();

        $pluginId = $this->getPropertyValue($xml, 'plugin');
        if ($pluginId === null) {
            throw new Exception('AU stanza does not have a plugin property.');
        }
        if (array_key_exists($pluginId, $pluginCache) && $this->em->contains($pluginCache[$pluginId])) {
            return $pluginCache[$pluginId];
        }
        $pluginRepo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Plugin');
        $plugin = $pluginRepo->findOneByIdentifier($pluginId);
        if ($plugin === null) {
            throw new Exception("Unknown pluginId: {$pluginId}");
        }
        $pluginCache[$pluginId] = $plugin;

        return $pluginCache[$pluginId];
    }

    /**
     * Get a content owner. They are cached in the static $ownerCache.
     * 
     * @staticvar array $ownerCache
     * @param type $name
     * @return ContentOwner|array
     */
    public function getContentOwner($name)
    {
        static $ownerCache = array();

        if (array_key_exists($name, $ownerCache) && $this->em->contains($ownerCache[$name])) {
            return $ownerCache[$name];
        }

        $owner = $this->em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')
            ->findOneBy(array(
            'name' => $name,
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
