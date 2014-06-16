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

class PLNImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lockssomatic:plnimport')
            ->setDescription('Import PLN plugins - using return value for testing.')
            ->addArgument('plugin_folder_path', InputArgument::OPTIONAL, 'Local path to the folder containing the PLN plugin JAR files?')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path_to_plugins = $input->getArgument('plugin_folder_path');
        if ($path_to_plugins){
            // Test countJarFilesInDir method
            $num_jar_files = $this->countJarFilesInDir($path_to_plugins);
            $text = "There are $num_jar_files plugin JAR files in the provided directory.";
            
            // Test extractJarFilesInDir method
            
            // Provide a way for temporary output directory to be distroyed after import?
            $output_directory = '/Applications/MAMP/htdocs/lockss-o-matic/tmp/pluginimport/output/';
            
            $array_of_extracted_jar_paths = $this->extractJarFilesInDir($path_to_plugins, $output_directory);
            
            $text .= implode(',', $array_of_extracted_jar_paths);
        
        } else {
            $text = 'No path was provided.  Aborting PLN plugin import.';
        }
        
        $output->writeln($text);
    }
    
    // methods related to ingestion of Lockss Plugins
    protected function countJarFilesInDir($dir_path)
    {
      $count = 0;
      $iterator = new \DirectoryIterator($dir_path);
      foreach ($iterator as $fileinfo) {
          $jar_string = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
          if($fileinfo->isFile() & $jar_string == 'jar'){
            $count +=1;
          }
      }
     return $count;
    } 
    
    protected function extractJarFilesInDir($dir_path, $output_directory)
    {
      # array to store the path of directory of extracted .jar files
      # return this array for use elsewhere
      $array_of_extracted_jar_paths = array();
      $iterator = new \DirectoryIterator($dir_path);
      foreach ($iterator as $fileinfo) 
      {
          $file = $fileinfo->getFilename();
          $file_path = $fileinfo->getPathname();
          $dir_file_is_in = pathinfo($file_path, PATHINFO_DIRNAME);
          $jar_string = pathinfo($file, PATHINFO_EXTENSION);
          $file_name_no_extension = pathinfo($file, PATHINFO_FILENAME);
          if($fileinfo->isFile() /*& $jar_string == 'jar'*/)
          {
              $zip = new \ZipArchive;
              $result = $zip->open($file_path);
              //echo '<br>' . $result;
              //pre_print_r($zip);
              //echo '<br>';
              if($result === TRUE)
              {
                $extract_to_dir = $output_directory . $file_name_no_extension;
                $zip->extractTo($extract_to_dir);
                $close_value = $zip->close();
                array_push($array_of_extracted_jar_paths, $extract_to_dir);
              } else {
                $close_value = $zip->close();
              }
          }
      }
      return $array_of_extracted_jar_paths; 
    }
    
    
    protected function determinePluginPathFromJarManifest($array_of_plugin_jar_paths)
    {
      ##
      # Determine plugin path from JAR manifest - bulk list
      # Return $array_of_plugin_xml_paths
      ##
      $array_of_plugin_xml_paths = array();
      foreach ($array_of_plugin_jar_paths as $plugin_jar_path) {
        //echo "<h3>$plugin_jar_path</h3>";
        //echo gettype($plugin_jar_path);
        //$path_to_extracted_jar = 'test_output/AlaskaStateDocsPlugin/';
        $path_to_extracted_jar = $plugin_jar_path;
  
        // Assume path to manifest file is standardized (for now).
        $manifest_file_rel_path = '/META-INF/MANIFEST.MF';

        // Once all the JAR files for the plugins have been extracted
        // into a directory, one can use a DirectoryIterator to get
        // the path_to_extracted_jar
        $path_to_manifest_file = $path_to_extracted_jar . $manifest_file_rel_path;

        $manifest_file = file_get_contents($path_to_manifest_file);
        $blocks = preg_split("#\n\s*\n#Uis", $manifest_file);

        foreach($blocks as $block){
          $modblock = repair_72char_break_in_jar_manifest_block($block);
          //pre_print_r($modblock);
          $manifest_block_array = explode("\n", $modblock);
          $manifest_block_array = rm_newline_from_array_element($manifest_block_array);
          //pre_print_r($manifest_block_array);
          # Note that in the MANIFEST.MF lines may not exceed 72 characters.
          # A number of plugins have lines in MANIFEST.MF that do.
          # According to 
          # http://docs.oracle.com/javase/7/docs/technotes/guides/jar/jar.html#JAR_Manifest
          # if a value should make an inital line longer than this, it is broken onto a new
          # line each starting with a single space.  This needs to be taken into account in
          # the parsing code.
          //pre_print_r($manifest_block_array);
    
          $rel_path_string = grab_plugin_rel_path($manifest_block_array);
          if($rel_path_string !=''){ 
            #echo '<br>'.$path_string;
            //echo $path_string;
            $path_to_plugin_xml = $path_to_extracted_jar . '/'. $rel_path_string;
            array_push($array_of_plugin_xml_paths, $path_to_plugin_xml);
          }
   
        }
      }
      
      return $array_of_plugin_xml_paths;
    
    } //  determinePluginPathFromJarManifest end 
    
    protected function rm_newline_from_array_element($someArray)
    {

      foreach ($someArray as $key => $value) {
        #echo "<br>Key: $key , Value: $value";    
        if(preg_match("/\s/", $value)){
          #echo "<br>Value before: " . $value;
          $value = preg_replace("/\s/", '', $value);
          $someArray[$key] = $value; 
          #echo "<br>Value after pre_replace: " . $value;
        }
    
      } 
      return $someArray;
    } // end rm_newline_from_array_element

    protected function repair_72char_break_in_jar_manifest_block($block)
    {
        ##
        # Examines a $block from the MANIFEST.MF file of a JAR pacakge
        # JAR Specification limits line lenght to 72 characters
        # http://docs.oracle.com/javase/7/docs/technotes/guides/jar/jar.html#JAR_Manifest
        # This causes class path names to be broken before the end of a path 
        # The function concatenates lines more than 72 characters back together from
        # from the broken form for easier interpretation elsewhere (outside of the JAR
        # specification context. 
        ##
        $block = preg_replace("/\n\s/",'', $block);
        return $block;
    } // end repair_72char_break_in_jar_manifest_block

    protected function grab_plugin_rel_path($manifest_block_array)
    {
        $result_array = array();
        foreach($manifest_block_array as $elmt){
          // search for string that ends with *Plugin.xml and store if found.
    
          // if string is 71 characters in lenght, the JAR spec says that
          // it must break onto a new line with a leading space.
    
          // if elmt is 71 characters in length and does not end with *Plugin.xml
          if(preg_match("/Plugin.xml$/i", $elmt)) {
            // Expected 'Name:rel_path_to_plugin';
            $parts_array = explode(':', $elmt);
          }
    
          if(preg_match("/Lockss-Plugin:true/i", $elmt)){
            //echo "<br>Indicated Lockss plugin";
            array_push($result_array, $parts_array[1]);
          }
        }
        // determine if one element of the array has value 'Lockss-Plugin: true'
        if(count($result_array) > 1){
            echo "<br>There was more than one indicated Plugin XML for this Lockks plugin.";
            exit("<br>Please investigate. Exiting.");
        }
  
        if(count($result_array) == 1){
          return $result_array[0];
        } else {
          return '';
        }
    } // end grab_plugin_rel_path

    protected function get_path_to_manifest($path_to_extracted_jar) 
    {
      $manifest_path = ''; 
      $iterator = new \DirectoryIterator($path_to_extracted_jar);
      foreach ($iterator as $fileinfo) {
        $file = $fileinfo->getFilename();
        //echo '<br>'.$file;
      }
  
      return $manifest_path;
    } // end get_path_to_manifest

    
    
    
    
}