<?php

namespace LOCKSSOMatic\ImportExportBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\PLNImporterBundle\Command\PLNPluginImportCommand;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class PLNPluginImportCommandTest extends KernelTestCase
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
    
    public function testGetPluginPath() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $manifest = <<<'ENDMANIFEST'
Name: org/lockss/plugin/bioone/BioOneAtyponNewPdfFilterFactory.class
SHA1-Digest: yqLWXrU6+lTuHO4AV5RyA3AdwN8=

Name: org/lockss/plugin/bioone/BioOneAtyponPlugin.xml
SHA1-Digest: zM3zZjsGM651/g1FVu2WjHNn7Bw=
Lockss-Plugin: true

Name: org/lockss/plugin/bioone/BioOneAtyponPdfFilterFactory$1.class
SHA1-Digest: nxzTYgSbbtGIbwLvuymhDIChoxw=   
ENDMANIFEST;
        
        $this->assertEquals('org/lockss/plugin/bioone/BioOneAtyponPlugin.xml', $command->getPluginPath($manifest));
    }
    
    public function testGetPluginPathLongLines() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $manifest = <<<'ENDMANIFEST'
Name: org/lockss/plugin/bioone/BioOneAtyponNewPdfFilterFactory.class
SHA1-Digest: yqLWXrU6+lTuHO4AV5RyA3AdwN8=

Name: org/lockss/plugin/bioone/org/lockss/plugin/bioone/org/
 lockss/plugin/bioone/BioOneAtyponPlugin.xml
SHA1-Digest: zM3zZjsGM651/g1FVu2WjHNn7Bw=
Lockss-Plugin: true

Name: org/lockss/plugin/bioone/BioOneAtyponPdfFilterFactory$1.class
SHA1-Digest: nxzTYgSbbtGIbwLvuymhDIChoxw=   
ENDMANIFEST;
        
        $this->assertEquals('org/lockss/plugin/bioone/org/lockss/plugin/bioone/org/lockss/plugin/bioone/BioOneAtyponPlugin.xml', $command->getPluginPath($manifest));
    }

    public function testFindXmlPropString() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $str = <<<'ENDXML'
<map>
  <entry>
    <string>plugin_status</string>
    <string>testing - MaryEllen</string>
  </entry>
  <entry>
    <string>plugin_identifier</string>
    <string>org.lockss.plugin.bioone.BioOneAtyponPlugin</string>
  </entry>
  <entry>
    <string>plugin_name</string>
    <string>BioOne Plugin (Atypon Systems Platform)</string>
  </entry>
</map>            
ENDXML;
        
        $xml = new SimpleXMLElement($str);
        $this->assertEquals('testing - MaryEllen', (string)$command->findXmlPropString($xml, 'plugin_status'));
        $this->assertEquals('org.lockss.plugin.bioone.BioOneAtyponPlugin', (string)$command->findXmlPropString($xml, 'plugin_identifier'));
        $this->assertEquals('BioOne Plugin (Atypon Systems Platform)', (string)$command->findXmlPropString($xml, 'plugin_name'));
        $this->assertEquals('', (string)$command->findXmlPropString($xml, 'foobar'));
        $this->assertEquals(null, $command->findXmlPropString($xml, 'foobar'));
    }

    /**
     * @expectedException Exception
     */
    public function testFindXmlPropStringException() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $str = <<<'ENDXML'
<map>
  <entry>
    <string>plugin_status</string>
    <string>testing - MaryEllen</string>
  </entry>
  <entry>
    <string>plugin_status</string>
    <string>org.lockss.plugin.bioone.BioOneAtyponPlugin</string>
  </entry>
</map>            
ENDXML;
        
        $xml = new SimpleXMLElement($str);
        $command->findXmlPropString($xml, 'plugin_status');
    }

    public function testFindXmlPropElement() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $str = <<<'ENDXML'
<map>
  <entry>
    <string>pcl</string>
    <list id='432'>
      <org.lockss.daemon.ConfigParamDescr>
        <key>base_url</key>        
      </org.lockss.daemon.ConfigParamDescr>
    </list>
  </entry>
  <entry>
    <string>rls</string>
    <list id='2214'>
      <org.lockss.daemon.ConfigParamDescr>
        <key>base_url</key>        
      </org.lockss.daemon.ConfigParamDescr>
    </list>
  </entry>
</map>            
ENDXML;
        
        $xml = new SimpleXMLElement($str);
        $element = $command->findXmlPropElement($xml, 'pcl');        
        $this->assertEquals('432', (string)$element['id']);
        
        $element = $command->findXmlPropElement($xml, 'foobarbax');
        $this->assertNull($element);
    }

    /**
     * @expectedException Exception
     */
    public function testFindXmlPropElementException() {
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $str = <<<'ENDXML'
<map>
  <entry>
    <string>pcl</string>
    <list id='432' />
  </entry>
  <entry>
    <string>pcl</string>
    <list id='2214' />
  </entry>
</map>            
ENDXML;
        
        $xml = new SimpleXMLElement($str);
        $command->findXmlPropElement($xml, 'pcl');        
    }

    /**
     * Functional test.
     */
    public function testImportPlugin() {
        /** @var EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        
        $command = new PLNPluginImportCommand();
        $command->setContainer($this->container);
        $xml = new SimpleXMLElement($this->getPluginXml());
        $command->importPlugin($xml);
        $em->flush();
        
        /** @var Plugins */
        $plugin = $em->getRepository('LOCKSSOMaticCRUDBundle:Plugins')->findOneBy(array(
            'name' => 'phony plugin for testing'
        ));
        $em->refresh($plugin);
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('LOCKSSOMatic\CRUDBundle\Entity\Plugins', $plugin);
        $this->assertEquals('phony plugin for testing', $plugin->getName());
        
        $properties = $plugin->getPluginProperties();
        $this->assertGreaterThan(0, count($properties));
        
        $em->remove($plugin);
        $em->flush();
    }
    
    private function getPluginXml() {
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
    <string>"BioOne Plugin (Atypon Systems Platform), Base URL %s, Journal ID %s, Volume %s", base_url, journal_id, volume_name</string>
  </entry>
</map>
ENDXML;
        return $str;
    }
    
}
