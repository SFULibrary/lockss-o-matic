<?php
// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNTitledbImportCommand.php
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
class PLNTitledbImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:plntitledbimport')
            ->setDescription('Import PLN titledb file - using return value for testing.')
            ->addArgument('path_to_titledb', InputArgument::OPTIONAL, 'Local path to the titledb xml file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToTitledb = $input->getArgument('path_to_titledb');
        if (file_exists($pathToTitledb)) {
            //$text = $pathToTitledb;
            $xml = simplexml_load_file($pathToTitledb);
            $text = "Loading the titledb XML.\n";
            $text .= "Depeding on the file size, this may take some time.\n";
            $text .= "Please be patient while the script runs.";
            $output->writeln($text);
            $AUs = $this->parseAUsFromTitledbXML($xml);
            $text = "The number of AUs is " . count($AUs);
            $output->writeln($text);
            
            $text = "Adding AUs:\n";
            foreach ($AUs as $au) {
                $result = $this->addAU($au);
                $text .= $result . "\n";
                break;
            }
            
            $output->writeln($text);

        } else {
            $text = 'An invalid path was provided.  Aborting PLN plugin import.';
            $output->writeln($text);
        }

        //$output->writeln($text);
    }

    protected function parseAUsFromTitledbXML($xml)
    {
      // Grab all AUs in the titledb xml
      // Root element of an AU is the property element with name "org.lockss.title"

      // All child property elements of parent wrapper element of org.lockss.title
      $xpathRule = '//lockss-config/property[@name="org.lockss.title"]/property';
      $AUs = $xml->xpath($xpathRule);

      return $AUs;
    }

    protected function addAU($au)
    {
        $xpathRule = 'property[@name="plugin"]';

        $pluginPropertyElement = $au->xpath($xpathRule)[0];
        $pluginIdentifier = $pluginPropertyElement->attributes()->value;

        return $pluginIdentifier;

        $pluginsId = $this->getPluginsId();
        //pre_print_r($plugins_id);
        //exit();

        // grab all the data from the AU - then do the INSERTS.
        // This will allow you to first get the plugin_indentifier value 
        //the top-level property (parent_id and property_value will be NULL)
        $au_top_level_property_key = 'au_top_level_property_name';  // value will be NULL for INSERT.
        $au_top_level_property_value = (string) $au->attributes()->name;

        // Be sure to cast to string before using in array to avoid Illegal offset type Warnings.
        //$au_top_level_property_key = (string) $au_top_level_property_key;

        $properties_key_value_array = array();
        $children_properties_of_top_level = $au->property;    
        foreach ($children_properties_of_top_level as $property) {
          //pre_print_r($property);
          //exit();
          // Be sure to cast to string before using in array to avoid Illegal offset type Warnings.
          $property_name = (string) $property->attributes()->name;
          $property_value = (string) $property->attributes()->value;

           //exit($property_name . "=>" . $property_value);
           //$properties_key_value_array[$property_name] = $property_value;
          if (preg_match('/^param\./', $property_name)) {
              $param_name = (string)$property_name; // Will be like param.1, param.2 
              // When inserting later, the key $param_name will have value NULL - but track 
              // parent_id (lastInsertId) when adding 
              $name_value_array = array(); // will hold the name(key) and value for the parameter

              // parameters for lockss plugin 
              // grab the parameter property with $property_name and any children.
              $xpath_rule = 'property[@name="'. $param_name . '"]/*';
              //pre_print_r($au);

              //echo $xpath_rule .'<br>';
              $param_name_values = $au->xpath($xpath_rule);
              //pre_print_r($param_name_values);
              //echo '<br>count($param_name_values) = ' . count($param_name_values);
              //echo "The number of parameter child property elements is ". count($param_name_values);

              // Add the name and value for the parameter from the children propety elements.
          
              //exit();
              foreach($param_name_values as $property_child) {
                //pre_print_r($property_child);
            
                $prop_key = (string) $property_child->attributes()->name;
                $prop_value = (string) $property_child->attributes()->value;
            
                $name_value_array[$prop_key] = $prop_value;
            
              }
          
              $properties_key_value_array[$property_name] = $name_value_array;
          } else {
            $properties_key_value_array[$property_name] = $property_value;
          }
      
        } 

        //pre_print_r($au);
        // Grab plugins_id via the plugins_properties table.
        // AU stanzas use plugin_identifier rather than plugin_name.
        // [Add plugin_identifier to plugins table - this may be more efficient.

        //pre_print_r($properties_key_value_array);
        //exit();
        // AUS table
        // Some data that must be inserted is not available.  Please double check with MJ.

        $query = $dbh->prepare('INSERT INTO aus (`plugins_id`) VALUES (?)');
        $query->execute(array($plugins_id));
        $aus_id = $dbh->lastInsertId();
        echo "Inserted a new AU with aus_id of $aus_id into the AUS table.";
    
        // au_properties table 
        // use $aus_id 
    
        // parent property element for AU
        $query = $dbh->prepare('INSERT INTO au_properties (`aus_id`, `property_key`) VALUES (?,?)');
        $property_key  = (string) $au_top_level_property_value = (string) $au->attributes()->name;
        $query->execute(array($aus_id,$property_key));
        $au_parent_id = $dbh->lastInsertID();
        foreach($properties_key_value_array as $key=>$value){
          //echo gettype($value);

          if(gettype($value) == 'array' && preg_match('/^param\./', $key)){
              // key will be param.n where n is some whole number.
              // if we get something else, then we need to investigate.

              // Insert $key with value NULL - record insert_id 
              $query = $dbh->prepare('INSERT INTO au_properties
                                        (
                                          `aus_id`,
                                          `parent_id`, 
                                          `property_key`
                                        )
                                        VALUES (?, ?, ?)
                                    ');
              $property_key = (string) $key;
              $query->execute(array($aus_id, $au_parent_id, $property_key)); 
              $parent_id = $dbh->lastInsertId();

              // iterate thought the key=>values in the value array using parent insert_id
              foreach($value as $child_key=>$child_value){
                $query = $dbh->prepare('INSERT INTO au_properties
                                          (
                                            `aus_id`,
                                            `parent_id`,
                                            `property_key`,
                                            `property_value`                                        
                                          )
                                        VALUE(?, ?, ?, ?)
                                      ');
                $property_key = (string) $child_key;
                $property_value = (string) $child_value;
                $query->execute(array($aus_id, $parent_id, $property_key, $property_value));
                //$last_au_prop_insert_id = $dbh->lastInsertId();
              }

          } else {
            /*
            $query = $dbh->prepare('INSERT INTO au_properties 
                                      (
                                        `aus_id`, 
                                        `parent_id`,
                                        `property_key`, 
                                        `property_value`
                                      ) 
                                      VALUES (?, ?, ?, ?)');
            $property_key = (string) $key;
            $property_value = (string) $value;
            $query->execute(array($aus_id, $au_parent_id, $property_key, $property_value));
            */
            //$last_au_prop_insert_id = $dbh->lastInsertId();
          }

        }

    }

    protected function getPluginsId(/*PDO $dbh, $plugin_identifier*/){
   
       // AUs only record the plugin_identifier
       //$query = $dbh->prepare('SELECT plugins_id FROM `plugin_properties` WHERE property_value = ? LIMIT 1;');
       //$query->execute(array($plugin_identifier));
       //$result = $query->fetch();
    
       return false;
    }

} // end of class