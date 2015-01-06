<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\PLNImporterBundle\Command\PLNPluginImportCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use \SimpleXMLElement;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-01-05 at 10:40:03.
 */
class PluginsTest extends KernelTestCase
{

    /**
     * @var Plugins
     */
    private $object;

    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        /** @var EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $xml = new SimpleXMLElement($this->getPluginXml());
        $command->importPlugin($xml);
        $em->flush();
        $em->clear();
        /** @var Plugins */
        $this->object = $em->getRepository('LOCKSSOMaticCRUDBundle:Plugins')->findOneBy(array(
            'name' => 'phony plugin for testing'
        ));
        $em->refresh($this->object);
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        /** @var EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        $em->remove($this->object);
        $em->flush();
    }

    public function testGetPluginIdentifier()
    {
        $this->assertInstanceOf('LOCKSSOMatic\CRUDBundle\Entity\Plugins', $this->object);
        $this->assertEquals('org.lockss.phony', $this->object->getPluginIdentifier());
    }

    public function testGetPluginConfigParams()
    {
        $params = $this->object->getPluginConfigParams();
        $this->assertEquals(4, count($params));
    }

    public function testGetDefinitionalProperties()
    {
        $params = $this->object->getDefinitionalProperties();
        $this->assertEquals(3, count($params));
        sort($params);
        $this->assertEquals('base_url', $params[0]);
        $this->assertEquals('journal_id', $params[1]);
        $this->assertEquals('volume_name', $params[2]);
    }

    private function getPluginXml()
    {
        $str = <<<'ENDXML'
<map>
  <entry>
    <string>plugin_identifier</string>
    <string>org.lockss.phony</string>
  </entry>
  <entry>
    <string>plugin_name</string>
    <string>phony plugin for testing</string>
  </entry>
  <entry>
    <string>plugin_version</string>
    <string>13</string>
  </entry>
  <entry>
    <string>required_daemon_version</string>
    <string>1.56.0</string>
  </entry>
  <entry>
    <string>plugin_config_props</string>
    <list>
      <org.lockss.daemon.ConfigParamDescr>
        <key>base_url</key>
        <displayName>Base URL</displayName>
        <description>Usually of the form http://&lt;journal-name&gt;.com/</description>
        <type>3</type>
        <size>40</size>
        <definitional>true</definitional>
        <defaultOnly>false</defaultOnly>
      </org.lockss.daemon.ConfigParamDescr>
      <org.lockss.daemon.ConfigParamDescr>
        <key>journal_id</key>
        <displayName>Journal Identifier</displayName>
        <description>Identifier for journal (often used as part of file names)</description>
        <type>1</type>
        <size>40</size>
        <definitional>true</definitional>
        <defaultOnly>false</defaultOnly>
      </org.lockss.daemon.ConfigParamDescr>
      <org.lockss.daemon.ConfigParamDescr>
        <key>username</key>
        <displayName>Username, unused</displayName>
        <description>Username - maybe used for login or something.</description>
        <type>1</type>
        <size>40</size>
        <definitional>false</definitional>
        <defaultOnly>false</defaultOnly>
      </org.lockss.daemon.ConfigParamDescr>
      <org.lockss.daemon.ConfigParamDescr>
        <key>volume_name</key>
        <displayName>Volume Name</displayName>
        <type>1</type>
        <size>20</size>
        <definitional>true</definitional>
        <defaultOnly>false</defaultOnly>
      </org.lockss.daemon.ConfigParamDescr>
    </list>
  </entry>
  <entry>
    <string>au_name</string>
    <string>"BioOne Plugin (Atypon Systems Platform), journal_id, volume_name</string>
  </entry>
</map>
ENDXML;
        return $str;
    }

}
