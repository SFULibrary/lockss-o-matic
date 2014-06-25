<?php
// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNImportCommand.php
namespace LOCKSSOMatic\PLNImporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CoreBundle\DependencyInjection\LomLogger;


use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;

/**
 * Private Lockss network plugin import command-line
 */
class PLNImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:plnimport')
            ->setDescription('Import PLN plugins - using return value for testing.')
            ->addArgument('plugin_folder_path', InputArgument::OPTIONAL, 'Local path to the folder containing the PLN plugin JAR files?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToPlugins = $input->getArgument('plugin_folder_path');
        if ($pathToPlugins) {
            // Test countJarFilesInDir method
            $numJarFiles = $this->countJarFilesInDir($pathToPlugins);
            $text = "There are $numJarFiles plugin JAR files in the provided directory.";
            $output->writeln($text);
            // Test extractJarFilesInDir method

            // Provide a way for temporary output directory to be distroyed after import?
            $outputDirectory = '/Applications/MAMP/htdocs/lockss-o-matic/tmp/pluginimport/output/';

            $arrayOfExtractedJarPaths = $this->extractJarFilesInDir($pathToPlugins, $outputDirectory);

            //$text .= implode(',', $arrayOfExtractedJarPaths);

            // Test of determinePluginPathFromJarManifest method
            $arrayOfPluginPaths = $this -> determinePluginPathFromJarManifest($arrayOfExtractedJarPaths);

            foreach ($arrayOfPluginPaths as $path) {
              if (file_exists($path)) {
                $xml = simplexml_load_file($path);
                //$text = 'Plugin XML has been loaded and is available via the $xml object.';
                $text = $this -> import_lockss_plugin($xml);
                $output -> writeln($text);
              } else {
                $text = 'Failed to load the plugin XML.';
                $text -> writeln($text);
              }
            }

        } else {
            $text = 'No path was provided.  Aborting PLN plugin import.';
        }

        //$output->writeln($text);
    }

    // methods related to ingestion of Lockss Plugins
    protected function countJarFilesInDir($dirPath)
    {
      $count = 0;
      $iterator = new \DirectoryIterator($dirPath);
      foreach ($iterator as $fileinfo) {
          $jarString = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
          if ($fileinfo->isFile() & $jarString == 'jar') {
            $count +=1;
          }
      }

      return $count;

    }

    protected function extractJarFilesInDir($dirPath, $outputDirectory)
    {
      /* array to store the path of directory of extracted .jar files
       return this array for use elsewhere 
      */
      $arrayOfExtractedJarPaths = array();
      $iterator = new \DirectoryIterator($dirPath);
      foreach ($iterator as $fileinfo) {
          $file = $fileinfo->getFilename();
          $filePath = $fileinfo->getPathname();
          $dirFileIsIn = pathinfo($filePath, PATHINFO_DIRNAME);
          $jarString = pathinfo($file, PATHINFO_EXTENSION);
          $fileNameNoExtension = pathinfo($file, PATHINFO_FILENAME);
          if ($fileinfo->isFile() /*& $jarString == 'jar'*/) {
              $zip = new \ZipArchive;
              $result = $zip->open($filePath);
              //echo '<br>' . $result;
              //pre_print_r($zip);
              //echo '<br>';
              if ($result === true) {
                $extractToDir = $outputDirectory . $fileNameNoExtension;
                $zip->extractTo($extractToDir);
                $closeValue = $zip->close();
                array_push($arrayOfExtractedJarPaths, $extractToDir);
              } else {
                $closeValue = $zip->close();
              }
          }
      }

      return $arrayOfExtractedJarPaths;
    }


    protected function determinePluginPathFromJarManifest($arrayOfExtractedJarPaths)
    {
      /**
      * Determine plugin path from JAR manifest - bulk list
      * Return $arrayOfPluginXmlPaths
      */
      $debugArray = array();
      $arrayOfPluginXmlPaths = array();
      foreach ($arrayOfExtractedJarPaths as $pluginJarPath) {

        $pathToExtractedJar = $pluginJarPath;

        // Assume path to manifest file is standardized (for now).
        $manifestFileRelPath = '/META-INF/MANIFEST.MF';

        // Once all the JAR files for the plugins have been extracted
        // into a directory, one can use a DirectoryIterator to get
        // the pathToExtractedJar
        $pathToManifestFile = $pathToExtractedJar . $manifestFileRelPath;

        $manifestFile = file_get_contents($pathToManifestFile);
        $blocks = preg_split("#\n\s*\n#Uis", $manifestFile);

        foreach ($blocks as $block) {
          $modblock = $this -> repair_72char_break_in_jar_manifest_block($block);

          $manifestBlockArray = explode("\n", $modblock);
          $manifestBlockArray = $this -> rm_newline_from_array_element($manifestBlockArray);
          /**
          *  Note that in the MANIFEST.MF lines may not exceed 72 characters.
          *  A number of plugins have lines in MANIFEST.MF that do.
          *  According to
          *  http://docs.oracle.com/javase/7/docs/technotes/guides/jar/jar.html#JAR_Manifest
          *  if a value should make an inital line longer than this, it is broken onto a new
          *  line each starting with a single space.  This needs to be taken into account in
          *  the parsing code.
          */
          $relPathString = $this -> grab_plugin_rel_path($manifestBlockArray);
          if ($relPathString !='') {
            //echo $path_string;
            $pathToPluginXml = $pathToExtractedJar . '/'. $relPathString;
            array_push($arrayOfPluginXmlPaths, $pathToPluginXml);
          }

        }
      }

      return $arrayOfPluginXmlPaths;

    } //  determinePluginPathFromJarManifest end 

    protected function rm_newline_from_array_element($someArray)
    {

      foreach ($someArray as $key => $value) {
        //echo "<br>Key: $key , Value: $value";
        if (preg_match("/\s/", $value)) {
          //echo "<br>Value before: " . $value;
          $value = preg_replace("/\s/", '', $value);
          $someArray[$key] = $value;
          //echo "<br>Value after pre_replace: " . $value;
        }

      }

      return $someArray;
    } // end rm_newline_from_array_element

    protected function repair_72char_break_in_jar_manifest_block($block)
    {
        /**
         * Examines a $block from the MANIFEST.MF file of a JAR pacakge
         * JAR Specification limits line lenght to 72 characters
         * http://docs.oracle.com/javase/7/docs/technotes/guides/jar/jar.html#JAR_Manifest
         * This causes class path names to be broken before the end of a path 
         * The function concatenates lines more than 72 characters back together from
         * from the broken form for easier interpretation elsewhere (outside of the JAR
         * specification context.
         */
        $block = preg_replace("/\n\s/", '', $block);

        return $block;
    } // end repair_72char_break_in_jar_manifest_block

    protected function grab_plugin_rel_path($manifestBlockArray)
    {
        $resultArray = array();
        foreach ($manifestBlockArray as $elmt) {
          /** 
           * Search for string that ends with *Plugin.xml and store if found.
           * If string is 71 characters in lenght, the JAR spec says that
           * it must break onto a new line with a leading space.
           * if elmt is 71 characters in length and does not end with *Plugin.xml
           */
          if (preg_match("/Plugin.xml$/i", $elmt)) {
            // Expected 'Name:rel_path_to_plugin';
            $partsArray = explode(':', $elmt);
          }

          if (preg_match("/Lockss-Plugin:true/i", $elmt)) {
            //echo "<br>Indicated Lockss plugin";
            array_push($resultArray, $partsArray[1]);
          }
        }
        // determine if one element of the array has value 'Lockss-Plugin: true'
        if (count($resultArray) > 1) {
            echo "<br>There was more than one indicated Plugin XML for this Lockks plugin.";
            exit("<br>Please investigate. Exiting.");
        }

        if (count($resultArray) == 1) {
          return $resultArray[0];
        } else {
          return '';
        }
    } // end grab_plugin_rel_path

    protected function get_path_to_manifest($pathToExtractedJar)
    {
      $manifestPath = '';
      $iterator = new \DirectoryIterator($pathToExtractedJar);
      foreach ($iterator as $fileinfo) {
        $file = $fileinfo->getFilename();
        //echo '<br>'.$file;
      }

      return $manifestPath;
    } // end get_path_to_manifest

    protected function import_lockss_plugin($xml)
    {
      // Add data to the plugins table

      // Instantiate an entity manager.
      $em = $this->getContainer()->get('doctrine')->getManager();

      // First check that the plugin has not already been 'registered' in the plugins table.

      //$pluginName = "Simon Fraser University Library Editorial Cartoons Collection Plugin";
      $pluginName = $this->get_plugin_name($xml);
      /*
      if ($this->plugin_exists_by_name($pluginName) == false) {
        return "$pluginName has yet to be imported.";
      } else {
        return 'Plugin already exists.';
      }
      */

      if ($this-> plugin_exists_by_name($pluginName) == false) {
          //echo 'Unknown plugin detected.  The plugin will now be added to the database.';
          // New lockss plugin - insert into plugin table
          /*
          $query = $dbh->prepare('INSERT INTO plugins (`name`) VALUES (?)');
          $query->execute(array($pluginName));
          $pluginsId = $dbh->lastInsertId();
          */
          $plugins = new Plugins();
          $plugins->setName($pluginName);
          $em->persist($plugins);
          $em->flush();
          $pluginId = $plugins->getId();

          // Grab the pertinent details of the plugin 
          // insert into the plugin_properties table

          // plugin_name parent_id null
          /*
          $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `property_key`, `property_value`) VALUES (?,?,?)');
          $query->execute(array($pluginsId, 'plugin_name', $pluginName));
          */
          $pluginProperties = new PluginProperties();
          $pluginProperties->setPluginsId($pluginId);
          $pluginProperties->setPropertyKey('plugin_name');
          $pluginProperties->setPropertyValue($pluginName);

          $em->persist($pluginProperties);
          $em->flush();

          //return "Addded entry into plugin_properties table for $pluginName with id $pluginId";

          //echo "<br>The plugin name $plugin_name has been added to the plugin properties table.";

          // plugin_version  parent_id null
          $pluginVersionEntryElement = $this -> get_plugin_entry_element_by_string_name($xml, 'plugin_version');
          /*
          if (is_object($pluginVersionEntryElement)) {
            $pluginVersionStringChildrenObj = $pluginVersionEntryElement->string;
          } else {
            return "PluginVersionEntryElement for $pluginName is of type " . (string) gettype($pluginVersionEntryElement) . " the value is $pluginVersionEntryElement";
          }
          */
          if (is_object($pluginVersionEntryElement) && ($pluginVersionEntryElement->string) == 2 ) {
              foreach ($pluginVersionStringChildrenObj as $key => $value) {
                  if ($value != 'plugin_version') {
                      $pluginVersionNum = $value;
                  }
              }
              /*
              $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `property_key`, `property_value`) VALUES (?,?,?)');
              $query->execute(array($pluginsId, 'plugin_version', $pluginVersionNum));
              */
              $pluginProperties->setPluginsId($pluginId);
              $pluginProperties->setPropertyKey('plugin_version');
              $pluginProperties->setPropertyValue($pluginVersionNum);

              $em->persist($pluginProperties);
              $em->flush();

              //return "You are importing version $pluginVersionNum of the $pluginName plugin.";
          } else {
              // Plugin XML may not inidcate plugin_version
              // Log these?
              //exit("There was an issue with the number of string child elements of the plugin version entry. Exiting.");
          }

          // plugin_identifier parent_id null
          $pluginIdentifierEntryElement = $this->get_plugin_entry_element_by_string_name($xml, 'plugin_identifier');
          $pluginIdentifierStringChildrenObj = $pluginIdentifierEntryElement->string;
          if (is_object($pluginIdentifierEntryElement) && count($pluginIdentifierEntryElement->string) == 2 ) {
              foreach ($pluginIdentifierStringChildrenObj as $key => $value) {
                  if ($value != 'identifier') {
                      $pluginIdentifier = $value;
                  }
              }
              /*
              $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `property_key`, `property_value`) VALUES (?,?,?)');
              $query->execute(array($pluginsId, 'plugin_identifier', $pluginIdentifier));
              */
              $pluginProperties->setPluginsId($pluginId);
              $pluginProperties->setPropertyKey('plugin_identifier');
              $pluginProperties->setPropertyValue($pluginIdentifier);

              $em->persist($pluginProperties);
              $em->flush();

              //return "The plugin identifier $pluginIdentifier was added to the database.";
          } else {
              // Plugin XML may not indicate plugin_identifier
              // log these?
              //exit("There was an issue with the number of string child elements of the plugin identifier entry. Exiting.");
          }

          // au_name [needed for aus_id?] parent_id null
          $auNameEntryElement = $this->get_plugin_entry_element_by_string_name($xml, 'au_name');
          $auNameStringChildrenObj = $auNameEntryElement->string;
          if (is_object($auNameEntryElement) && count($auNameEntryElement->string) == 2 ) {
              foreach ($auNameStringChildrenObj as $key => $value) {
                  if ($value != 'identifier') {
                      $auName = $value;
                  }
              }
              /*
              $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `property_key`, `property_value`) VALUES (?,?,?)');
              $query->execute(array($pluginsId, 'au_name', $auName));
              */
              $pluginProperties->setPluginsId($pluginId);
              $pluginProperties->setPropertyKey('au_name');
              $pluginProperties->setPropertyValue($auName);

              $em->persist($pluginProperties);
              $em->flush();

              //return "The au name $auName for plugin $pluginId was added to the database.";
          } else {
              // Plugin XML may not indicate au_name
              // Log these?              
              //exit("There was an issue with the number of string child elements of the au_name entry. Exiting.");
          }

          // plugin_config_props
          // plugin_config_props will be key of parent (no value)
          // children will the be the ConfigParamDescr track parent id

          $pluginParameters = $this->get_plugin_entry_element_by_string_name($xml, 'plugin_config_props');

          if (is_object($pluginParameters)) {

            $pluginParameters = $pluginParameters->list->{'org.lockss.daemon.ConfigParamDescr'};

            // Add a row with key plugin_config_props  and null value record insert_id as parent 
            /*
            $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`,`property_key`) VALUES (?,?)');
            $query->execute(array($pluginsId, 'plugin_config_props'));
            */
            $pluginProperties->setPluginsId($pluginId);
            $pluginProperties->setPropertyKey('plugin_config_props');

            $em->persist($pluginProperties);
            $em->flush();

            //$pluginConfigPropsRowId = $dbh->lastInsertId();
            $pluginConfigPropsRowId = $pluginProperties->getId();

            //return "Added plugin_config_props with row id $pluginConfigPropsRowId into database for plugin $pluginId";

            foreach ($pluginParameters as $element) {

                // for each list item, add a row as a child or parent with key of configparamdescr and value null record
                /*
                $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `parent_id`, `property_key`) VALUES (?,?,?)');
                $query->execute(array($pluginsId, $pluginConfigPropsRowId, 'configparamdescr'));
                */
                $pluginProperties->setPluginsId($pluginId);
                $pluginProperties->setParentId($pluginConfigPropsRowId);
                $pluginProperties->setPropertyKey('configparamdescr');

                $em->persist($pluginProperties);
                $em->flush();

                //$configparamdescrId = $dbh->lastInsertId();
                $configparamdescrId = $pluginProperties->getId();
                // record insert ID to group the properties of the given paramenter
                //pre_print_r($element);
                foreach ($element as $key => $value) {
                    /*
                    $query = $dbh->prepare('INSERT INTO plugin_properties (`plugins_id`, `parent_id`, `property_key`, `property_value`) VALUES (?,?,?,?)');
                    $query->execute(array($pluginsId, $configparamdescrId, $key, $value));
                    */
                    $pluginProperties->setPluginsId($pluginId);
                    $pluginProperties->setParentId($configparamdescrId);
                    $pluginProperties->setPropertyKey($key);
                    $pluginProperties->setPropertyValue($value);

                    $em->persist($pluginProperties);
                    $em->flush();

                }

            }

          } else {
            // Plugin XML may not indicate configuration parameters.
            // Log these?            
            //return "Plugin $pluginName may not have any configuration parameters.  Please investigate."
          }

          return "$pluginName has been added to the database.";
      } else {
          return "The $pluginName has previously been added.";
      }
    } // End of import_lockss_plugin method

    protected function plugin_exists_by_name($pluginName)
    {
        // Instantiate an entity manager.
        $em = $this->getContainer()->get('doctrine')->getManager();

        $result = false;

        // Determine if the plugin of the same name already exists in the DB.
        $result = $em
                    ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Plugins')
                    ->findOneByName($pluginName);

        return (bool) $result;
    } // End of plugin_exists_by_name


    protected function get_plugin_entry_element_by_string_name($pluginXmlObj, $stringName)
    {
      /**
       * plugin xml has <entry> elements with one or more <string> child elements 
       * [Is it valid to have no <string> child elements of <entry>?
       */
       $xml = $pluginXmlObj;

       // return all entry elements with string child element with text value = plugin name string.
       $xpathRule = "//entry[./string='". $stringName. "']";

       $result = $xml->xpath($xpathRule);
       //echo "<h1>plugin_name entry element - count ". count($result) ."</h1><pre>";
       //print_r($result);
       //echo "</pre>";

       // Assume xpath search returns only one entry element.
       if (count($result) == 1) {
          // returns SimpleXMLObject
          return $result[0];
       } else {
          return "There is more than one entry element in the plugin XML with child string element containing $stringName";
       }
    } // end of get_plugin_entry_element_by_string_name

    protected function get_plugin_name($xml)
    {

        $pluginNameEntryElementObj = $this -> get_plugin_entry_element_by_string_name($xml, 'plugin_name');

        $stringElements = $pluginNameEntryElementObj->string;

        // Assume that there should only be two string elements.
        if (count($stringElements == 2)) {

           foreach ($stringElements as $key => $value) {
              if ($value != 'plugin_name') {
                  $pluginName = (string) $value;
              }
           }

        } else {
          // Indicate error to admin user somehow
          $pluginName = 'Unable to get plugin name. ';
          $pluginName .= 'There was an unexpected number of string child elements ';
          $pluginName .= 'of the entry element containing the plugin_name.';
        }

        return $pluginName;
    }

} // end of class