<?php
// src/LOCKSSOMatic/PLNImporterBundle/Command/PLNTitledbImportCommand.php
namespace LOCKSSOMatic\PLNImporterBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Exception;
use LOCKSSOMatic\CoreBundle\DependencyInjection\LomLogger;
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            $xml = simplexml_load_file($pathToTitledb);
            $text = "Loading the titledb XML.\n";
            $text .= "Depeding on the file size, this may take some time.\n";
            $text .= "Please be patient while the script runs.";
            $output->writeln($text);
            $AUs = $this->parseAUsFromTitledbXML($xml);
            $text = "The number of AUs is " . count($AUs);
            $output->writeln($text);

            $text = "Adding AUs:\n";
            $output->writeln($text);
            $count = 0;
            foreach ($AUs as $au) {
                $gcValue = gc_collect_cycles(); // force PHP garbage collection.
                $result = $this->addAU($au);
                $count+=1;
                $text = "Result: $result . Added $count AUs. GC value = $gcValue";
                $output->writeln($text);
            }

            $text = "Completing added AUs.  Please verify.";
            $output->writeln($text);

        } else {
            $text = 'An invalid path was provided.  Aborting PLN plugin import.';
            $output->writeln($text);
        }
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

    protected function addAU($auXml)
    {
        $xpathRule = 'property[@name="plugin"]';

        $pluginPropertyElement = $auXml->xpath($xpathRule)[0];
        $pluginIdentifier = $pluginPropertyElement->attributes()->value;

        try {
          $plugin = $this->getPlugin($pluginIdentifier);
        } catch (Exception $exception) {
          return "Exception: " . $e->getMessage() .  "\n PluginIdentifier: $pluginIdentifier";
        }

//        // grab all the data from the AU
//        // First get the pluginIndentifier value
//        // the top-level property (parentId and propertyValue will be NULL)
        $auTopLevelPropertyKey = 'au_top_level_property_name';  // value will be NULL for INSERT.
//        // Cast to string before using in array to avoid illegal offset type Warnings.
        $auTopLevelPropertyValue = (string) $auXml->attributes()->name;
//
        $propertiesKeyValueArray = array();
        $childrenPropertiesOfTopLevel = $auXml->property;
        
        foreach ($childrenPropertiesOfTopLevel as $property) {
          // cast to string before using in array to avoid Illegal offset type warnings.
          $propertyName = (string) $property->attributes()->name;
          $propertyValue = (string) $property->attributes()->value;

          if (preg_match('/^param\./', $propertyName)) {
              $paramName = (string) $propertyName; // Will be like param.1, param.2 
              // The key $paramName will have value NULL - but track parentId
              $nameValueArray = array(); // will hold the name(key) and value for the parameter

              // parameters for lockss plugin 
              // grab the parameter property with $propertyName and any children.
              $xpathRule = 'property[@name="'. $paramName . '"]/*';
              $paramNameValues = $auXml->xpath($xpathRule);

              // Add the name and value for the parameter from the children propety elements.
              foreach ($paramNameValues as $propertyChild) {

                $propKey = (string) $propertyChild->attributes()->name;
                $propValue = (string) $propertyChild->attributes()->value;

                $nameValueArray[$propKey] = $propValue;

              }

              $propertiesKeyValueArray[$propertyName] = $nameValueArray;
          } else {
            $propertiesKeyValueArray[$propertyName] = $propertyValue;
          }

        }

        // Grab pluginsId via the pluginsProperties table.
        // AU stanzas use pluginIdentifier rather than pluginName.
        // [Add pluginIdentifier to plugins table?]

        // Instantiate an entity manager.
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        // Aus table - add plugins id into Aus table.
        $aus = new Aus();
        $plugin->addAus($aus);
        $aus->setPlugin($plugin);
        $em->persist($aus);
        $em->flush();
        
//        $em->clear();
//
        $auProperties = new AuProperties();
//
        $auProperties->setAu($aus);
        $propertyKey = (string) $auXml->attributes()->name;
        $auProperties->setPropertyKey($propertyKey);
        $em->persist($auProperties);
        $em->flush();
//        
        $auParent = $auProperties;
//        $em->clear();
//
        foreach ($propertiesKeyValueArray as $key => $value) {
          if (gettype($value) == 'array' && preg_match('/^param\./', $key)) {
              // key will be param.n where n is some whole number.
              // if we get something else, then we need to investigate.
              // Instantiate an entity manager.
                        
              // $key with value NULL - record insertId 
              $auProperties = new AuProperties();
              $auProperties->setAu($aus);
              $auProperties->setParent($auParent);
              $propertyKey = (string) $key;
              $auProperties->setPropertyKey($propertyKey);
              $em->persist($auProperties);
              $em->flush();
              $parent = $auProperties;

              // iterate thought the key=>values in the value array using parent insertId
              foreach ($value as $childKey => $childValue) {
                $auProperties = new AuProperties();
                $auProperties->setParent($auParent);
                $propertyKey = (string) $childKey;
                $propertyValue = (string) $childValue;
                $auProperties->setPropertyKey($propertyKey);
                $auProperties->setPropertyValue($propertyValue);
                $auProperties->setAu($aus);
                $em->persist($auProperties);
                $em->persist($aus);
                $em->flush();
              }

          } else {
            /*
            $query = $dbh->prepare('INSERT INTO auProperties 
                                      (
                                        `ausId`, 
                                        `parentId`,
                                        `propertyKey`, 
                                        `propertyValue`
                                      ) 
                                      VALUES (?, ?, ?, ?)');
            $propertyKey = (string) $key;
            $propertyValue = (string) $value;
            $query->execute(array($ausId, $auParentId, $propertyKey, $propertyValue));
            */
            //$lastAuPropInsertId = $dbh->lastInsertId();
          }

        }

    }

    /**
     * Get a plugin based on its identifier property.
     * 
     * @param type $pluginIdentifier
     * @return type
     */
    protected function getPlugin($pluginIdentifier)
    {

        // AUs only record the pluginIdentifier

        // Instantiate an entity manager.
        $em = $this->getContainer()->get('doctrine')->getManager();

        //$repository = $em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\PluginProperties')->findOneBy(array('propertyValue' => $pluginIdentifier));
        $result = $em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\PluginProperties')
                    ->findOneByPropertyValue($pluginIdentifier);
        return $result->getPlugin();
    }

} // end of class