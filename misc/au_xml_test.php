<?php

/**
 * Proof of concept script to output the XML fragment used in LOCKSS'
 * titledb.xml file to define a single AU, using LOCKSS-O-Matic's database
 * as the source of the configuration data.
 *
 * Outputs XML like:
 *
 * <?xml version="1.0"?>
 * <property name="SFUCartoonsPluginSimonFraserUniversityLibraryETDs20141">
 *  <property name="title" value="Simon Fraser University Library ETDs 2014 part 1"/>
 *  <property name="attributes.publisher" value="Simon Fraser University Library"/>
 *  <property name="journalTitle" value="Simon Fraser University Library ETDs"/>
 *  <property name="plugin" value="ca.sfu.lib.plugin.etds.SFUETDsPlugin"/>
 *  <property name="param.1">
 *   <property name="base_url" value="http://someserver.lib.sfu.ca/etds/"/>
 *  </property>
 *  <property name="param.2">
 *   <property name="year" value="2014"/>
 *  </property>
 *  <property name="param.3">
 *   <property name="part" value="1"/>
 *  </property>
 * </property>
 *
 * Note: The output contains an XML declaration. In production, we don't want this
 * (in fact, can't have it) since the output is an XML fragment, not a complete
 * document.
 */

$au_id = trim($argv[1]);
$database = 'lockssomatic';
$username = 'lockssomatic';
$password = 'foo';

$dbh = new PDO('mysql:host=localhost;dbname=' . $database, $username, $password);

// First get the top-level property (parent_id and property_value will be NULL).
$query = $dbh->prepare("SELECT id, property_key FROM au_properties WHERE aus_id = ? AND parent_id is null AND property_value is null");
$query->execute(array($au_id));
$result = $query->fetch();
$top_level_id = $result['id'];
// Write out the XML element for this property.
$au_XML = new SimpleXMLElement("<property></property>");
$au_XML->addAttribute('name', $result['property_key']);

// Query all the immediate children of the top-level property.
$query = $dbh->prepare("SELECT id, property_key, property_value FROM au_properties WHERE aus_id = ? AND parent_id = ?");
$query->execute(array($au_id, $top_level_id));
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
if (count($rows)) {
  foreach ($rows as $row) {
    $child = $au_XML->addChild('property');
    $child->addAttribute('name', $row['property_key']);
    // If the current row is a 'param.x' row, query for its child properties
    // and add them to the XML.
    if (preg_match('/^param\./', $row['property_key'])) {
      // Query all the immediate children of the 'param.x' properties.
      $query = $dbh->prepare("SELECT id, property_key, property_value FROM au_properties WHERE aus_id = ? AND parent_id = ?");
      $query->execute(array($au_id, $row['id']));
      $param_rows = $query->fetchAll(PDO::FETCH_ASSOC);
      // If the 'param.x' row has children, add them.
      if (count($param_rows)) {
        foreach ($param_rows as $param_row) {
          $property_child = $child->addChild('property');
          $property_child->addAttribute('name', $param_row['property_key']);
          $property_child->addAttribute('value', $param_row['property_value']);
        }
      }
    }
    else {
      // For non-'param.x' rows in this loop, add the 'value' attribute value.
      $child->addAttribute('value', $row['property_value']);
    }
  }
} 

print $au_XML->asXML();
?>
