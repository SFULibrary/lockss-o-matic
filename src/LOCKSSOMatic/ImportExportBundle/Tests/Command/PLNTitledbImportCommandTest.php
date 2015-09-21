<?php

namespace LOCKSSOMatic\ImportExportBundle\Tests\Command;

use Exception;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\PLNImporterBundle\Command\PLNTitledbImportCommand;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class PLNPTitledbImportCommandTest extends KernelTestCase
{

    /** @var Container */
    private $container;

    // unit tests first.

    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    public function testGetPropertyValue() {
        $command = new PLNTitledbImportCommand();
        $command->setContainer($this->container);
        $xmlStr = <<<'ENDXML'
  <property name="BioOneAtyponPluginRadiationResearch169">
   <property name="attributes.publisher" value="Radiation Research Society" />
   <property name="issn" value="0033-7587" />
  </property>
ENDXML;
        $xml = new SimpleXMLElement($xmlStr);
        $this->assertEquals(
            'Radiation Research Society',
            $command->getPropertyValue($xml, 'attributes.publisher')
        );
        $this->assertEquals(null, $command->getPropertyValue($xml, 'foobar'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetPropertyValueException() {
        $command = new PLNTitledbImportCommand();
        $command->setContainer($this->container);
        $xmlStr = <<<'ENDXML'
  <property name="BioOneAtyponPluginRadiationResearch169">
   <property name="attributes.publisher" value="Radiation Research Society" />
   <property name="attributes.publisher" value="0033-7587" />
  </property>
ENDXML;
        $xml = new SimpleXMLElement($xmlStr);
        $this->assertEquals(
            'Radiation Research Society',
            $command->getPropertyValue($xml, 'attributes.publisher')
        );
    }

    public function testGetPlugin() {
        $command = new PLNTitledbImportCommand();
        $command->setContainer($this->container);
        $em = $this->container->get('doctrine')->getManager();

        /// create a plugin.
        $plugin = new Plugins();
        $plugin->setName('Foo');
        $em->persist($plugin);

        // and set its identifier property
        $pluginProp = new PluginProperties();
        $pluginProp->setPlugin($plugin);
        $pluginProp->setPropertyKey('plugin_identifier');
        $pluginProp->setPropertyValue('org.example.test.Foo');
        $em->persist($pluginProp);
        $em->flush();

        // now get the plugin via the command.
        $fetchedPlugin = $command->getPlugin('org.example.test.Foo');
        $this->assertNotNull($fetchedPlugin);
        $this->assertInstanceOf('LOCKSSOMatic\CRUDBundle\Entity\Plugins', $fetchedPlugin);

        $em->remove($pluginProp);
        $em->remove($plugin);
        $em->flush();
    }

    public function testGetContentOwner() {
        $command = new PLNTitledbImportCommand();
        $command->setContainer($this->container);
        $em = $this->container->get('doctrine')->getManager();

        /// create a plugin.
        $plugin = new Plugins();
        $plugin->setName('Foo');
        $em->persist($plugin);
        $em->flush();

        // now find/get a content owner for the plugin by name.
        $owner = $command->getContentOwner('foobar', $plugin);
        $this->assertNotNull($owner);
        $this->assertInstanceOf('LOCKSSOMatic\CRUDBundle\Entity\ContentOwners', $owner);
        $this->assertEquals('foobar', $owner->getName());
        $this->assertEquals($plugin, $owner->getPlugin());

        // fetch it again - check the caching.
        $cachedOwner = $command->getContentOwner('foobar', $plugin);
        $this->assertNotNull($cachedOwner);
        $this->assertInstanceOf('LOCKSSOMatic\CRUDBundle\Entity\ContentOwners', $cachedOwner);
        $this->assertEquals('foobar', $cachedOwner->getName());
        $this->assertEquals($plugin, $cachedOwner->getPlugin());
        $this->assertEquals($owner, $cachedOwner);

        $em->remove($owner);
        $em->remove($plugin);
        $em->flush();
    }

    /**
     * Functional test
     */
    public function testAddAu() {
        $command = new PLNTitledbImportCommand();
        $command->setContainer($this->container);
        $em = $this->container->get('doctrine')->getManager();

        /// create a plugin.
        $plugin = new Plugins();
        $plugin->setName('Foo');
        $em->persist($plugin);

        // and set its identifier property
        $pluginProp = new PluginProperties();
        $pluginProp->setPlugin($plugin);
        $pluginProp->setPropertyKey('plugin_identifier');
        $pluginProp->setPropertyValue('org.example.test.Foo');
        $em->persist($pluginProp);
        $em->flush();

        $xmlStr = $this->getXml();
        $xml = new SimpleXMLElement($xmlStr);
        $command->addAu($xml);

        $em->flush(); // addAu doesn't flush().
        $plugin = $em
            ->getRepository('LOCKSSOMaticCRUDBundle:Plugins')
            ->findOneBy(array('name' => 'Foo'));
        
        $em->refresh($plugin);
        $aus = $plugin->getAus();
        $this->assertNotNull($aus);
        $this->assertGreaterThan(0, $aus->count());
        $this->assertEquals($plugin, $aus[0]->getPlugin());

        $em->remove($aus[0]);
        $em->remove($pluginProp);
        $em->remove($plugin);
        $em->flush();
    }

    private function getXml() {
        $str = <<<'ENDXML'
<property name="Foo">
   <property name="attributes.publisher" value="Radiation Research Society" />
   <property name="journalTitle" value="Radiation Research" />
   <property name="issn" value="1234-5678" />
   <property name="eissn" value="3321-1234" />
   <property name="type" value="journal" />
   <property name="title" value="Foo Research Volume 16" />
   <property name="plugin" value="org.example.test.Foo" />
   <property name="param.1">
    <property name="key" value="base_url" />
    <property name="value" value="http://foo.example.com/" />
   </property>
   <property name="param.2">
    <property name="key" value="journal_id" />
    <property name="value" value="rare" />
   </property>
   <property name="param.3">
    <property name="key" value="volume_name" />
    <property name="value" value="16" />
   </property>
   <property name="attributes.where" value="AUtest" />
   <property name="attributes.year" value="2010" />
  </property>
ENDXML;
        return $str;
    }

}