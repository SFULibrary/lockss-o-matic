<?php

// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNPluginImportCommand.php

namespace LOCKSSOMatic\PLNImporterBundle\Command;

use DirectoryIterator;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use SimpleXMLElement;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ZipArchive;

/**
 * Private Lockss network plugin import command-line
 */
class PLNPluginImportCommand extends ContainerAwareCommand
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

    /**
     * Configure the command by adding arguments.
     */
    protected function configure()
    {
        $this->setName('lockssomatic:plnpluginimport')
            ->setDescription('Import PLN plugins.')
            ->addArgument('plugin_folder_path', InputArgument::REQUIRED, 'Local path to the folder containing the PLN plugin JAR files?');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToPlugins = $input->getArgument('plugin_folder_path');
        $jarFiles = $this->getJarFiles($pathToPlugins);

        $output->writeln("There are " . count($jarFiles) . " JAR files in the directory.");

        foreach ($jarFiles as $fileInfo) {
            $result = $this->processJarFile($fileInfo);
            if($result !== null) {
                $output->writeln($result);
            } else {
                $output->writeln("Imported {$fileInfo->getFileName()}");
            }
        }
        $this->em->flush();
    }

    public function processJarFile(SplFileInfo $fileInfo) {
        $zip = new ZipArchive();
        $res = $zip->open($fileInfo->getPathname());
        if($res !== true) {
            return " ** Error: Cannot open {$fileInfo->getPathName()}. Error code {$res}.";
            
        }            
        $manifest = $zip->getFromName('META-INF/MANIFEST.MF');
        $pluginPath = $this->getPluginPath($manifest);
        $pluginData = $zip->getFromName($pluginPath);
        $pluginXml = new SimpleXMLElement($pluginData);
        try {
            $this->importPlugin($pluginXml);
        } catch(Exception $e) {
            return " ** Error processing {$fileInfo->getFileName()}: {$e->getMessage()}";
        }
        return null;
    }
    
    /**
     * Get the JAR files in a directory.
     *
     * @param type $dirPath
     *
     * @throws UnexpectedValueException if the path cannot be opened.
     * @throws RuntimeException if the path is an empty string.
     *
     * @return SplFileInfo[]
     */
    protected function getJarFiles($dirPath)
    {
        $files = array();
        $iterator = new DirectoryIterator($dirPath);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->getExtension() === 'jar') {
                $files[] = $fileInfo->getFileInfo();
            }
        }
        return $files;
    }

    /**
     * Find the plugin .xml file from the manifest file.
     *
     * @param string $rawManifest the Jar file manifest.
     *
     * @return string
     */
    public function getPluginPath($rawManifest)
    {
        $manifest = preg_replace('/\r\n/', "\n", $rawManifest);
        $blocks = preg_split('/\n\s*\n/s', $manifest);

        foreach ($blocks as $block) {
            if (preg_match('/^\s*$/', $block)) {
                continue;
            }

            $block = preg_replace("/\n\s(.)/", '\1', $block);
            $keys = array();
            foreach (preg_split('/\n/', $block) as $line) {
                list($k, $v) = preg_split('/\s*:\s*/', $line);
                $keys = array_merge($keys, array($k => $v));
            }
            if (array_key_exists('Lockss-Plugin', $keys) && $keys['Lockss-Plugin'] === 'true') {
                return $keys['Name'];
            }
        }
        return '';
    }

    /**
     * Find a property string in a LOCKSS plugin.xml file.
     *
     * @param SimpleXMLElement $xml
     * @param string $propName
     *
     * @return string
     *
     * @throws Exception
     */
    public function findXmlPropString(SimpleXMLElement $xml, $propName)
    {
        $data = $xml->xpath("//entry[string[1]/text() = '{$propName}']/string[2]");
        if (count($data) === 1) {
            return $data[0];
        }
        if (count($data) === 0) {
            return null;
        }
        throw new Exception('Too many entry elements for property string ' . $propName);
    }

    /**
     * Find a list element in a LOCKSS plugin.xml file.
     *
     * @param SimpleXMLElement $xml
     * @param type $propName
     *
     * @return SimpleXMLElement
     *
     * @throws Exception
     */
    public function findXmlPropElement(SimpleXMLElement $xml, $propName)
    {
        $data = $xml->xpath("//entry[string[1]/text() = '{$propName}']/list");
        if (count($data) === 1) {
            return $data[0];
        }
        if (count($data) === 0) {
            return null;
        }
        throw new Exception('Too many entry elements for property element' . $propName);
    }

    /**
     * Generate and persist a new Plugins object.
     *
     * @param Plugins $plugin
     * @param string $name
     * @param string $value
     *
     * @return PluginProperties
     */
    public function newPluginProperty(Plugins $plugin, $name, $value)
    {
        $property = new PluginProperties();
        $property->setPlugin($plugin);
        $property->setPropertyKey($name);
        $property->setPropertyValue((string)$value);
        $this->em->persist($property);
        return $property;
    }

    /**
     * Import the data from the plugin. Does not create content
     * owners for the plugins, that's handled by the titledb import
     * command.
     *
     * @param SimpleXMLElement $xml
     */
    public function importPlugin(SimpleXMLElement $xml)
    {
        $pluginRepo = $this->em->getRepository('LOCKSSOMaticCRUDBundle:Plugins');

        $pluginName = $this->findXmlPropString($xml, 'plugin_name');
        if ($pluginRepo->findOneBy(array('name' => $pluginName)) !== null) {
            throw new Exception('Plugin has already been imported.');
        }

        $plugin = new Plugins();
        $plugin->setName($pluginName);
        $this->em->persist($plugin);

        $this->newPluginProperty($plugin, 'plugin_name', $pluginName);
        $this->newPluginProperty($plugin, 'plugin_version', $this->findXmlPropString($xml, 'plugin_version'));
        $this->newPluginProperty($plugin, 'plugin_identifier', $this->findXmlPropString($xml, 'plugin_identifier'));
        $this->newPluginProperty($plugin, 'au_name', $this->findXmlPropString($xml, 'au_name'));

        $configProps = $this->findXmlPropElement($xml, 'plugin_config_props');
        if ($configProps === null) {
            throw new Exception('No PluginConfigProps element.');
        }

        $parameters = $configProps->children();
        $rootProp = $this->newPluginProperty($plugin, 'plugin_config_props', null);

        foreach ($parameters as $element) {
            $pluginProperties = $this->newPluginProperty($plugin, 'configparamdescr', null);
            $pluginProperties->setParent($rootProp);
            foreach ($element as $key => $value) {
                $childProp = $this->newPluginProperty($plugin, $key, $value);
                $childProp->setParent($pluginProperties);
            }
        }
    }
}
