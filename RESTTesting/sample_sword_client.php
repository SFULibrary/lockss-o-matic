<?php

/**
 * Sample SWORD v2 client for LOCKSS-O-Matic that illustrates retrieving the
 * Service Document, creating the deposit, requesting the Deposit Reciept, and
 * editing the deposit content.
 * 
 * Uses the SWORD v2 PHP client library (https://github.com/swordapp/swordappv2-php-library),
 * which is not distributed with LOCKSS-O-Matic.
 * 
 * LOCKSS-O-Matic implements a minimalist SWORD v2 server. All deposits are created
 * using Atom files which contain <lom:content> entries identifying the URLs identifying
 * content that will be preserved in Private LOCKSS Networks managed by LOCKSS-O-Matic.
 * See the atom.xml that accompanies this script for a usage example. In summary, each
 * content entry looks like this:
 * 
 * <lom:content size="102400"
 *   checksumType="md5"
 *   checksumValue="bd4a9b642562547754086de2dab26b7d">
 *     http://contentprovider.example.org/download/file.zip
 * </lom:content>
 */

// The path to your swordappv2-php library.
$sword_app_library = "swordappv2-php-library/swordappclient.php";

// Value of the X-On-Behalf-Of HTTP header. This is the ID of the LOCKSS-O-Matic
// content provider making the deposit.
$on_behalf_of = "1";

// The base URL of your LOCKSS-O-Matic instance. This should be the same
// as the "Base URL" value in your LOM Settings.
$lom_base_url = "http://localhost/lockss-o-matic/web/app_dev.php";

/**
 * You do not need to change anything below this line.
 */

// The Service Document IRI.
$sd_url = $lom_base_url . "/api/sword/2.0/sd-iri";

// The Col-IRI (where the Atom document creating the deposit is POSTed).
$deposit_url = $lom_base_url . "/api/sword/2.0/col-iri/" . $on_behalf_of;

// The Atom entry used to deposit the content.
$atom_entry_create = "atom_create.xml";

// The Atom entry used to modify the content.
$atom_entry_modify = "atom_modify.xml";

// Include the library and instantiate a new SWORD client.
require($sword_app_library);
$sword_client = new SWORDAPPClient();

// Get the Service Document response.
print "Retrieving Service Document from " . $sd_url . "\n";
$sd_response = $sword_client->servicedocument($sd_url, '', '', $on_behalf_of);
print "Received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
if ($sd_response->sac_status == 200) {
    $sd_response->toString();
}

// Create the deposit.
print "\nCreating deposit via Atom entry (" . $atom_entry_create . ") to " . $deposit_url . "\n";
$sd_response = $sword_client->depositAtomEntry($deposit_url, '', '', $on_behalf_of, $atom_entry_create, false);
if ($sd_response->sac_status == 201) {
    print "OK, received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
}
else {
    print "Received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
}

// Get the IRIs.
$edit_iri = $sd_response->sac_edit_iri;
$cont_iri = $sd_response->sac_content_src;
$edit_media = $sd_response->sac_edit_media_iri;
$statement_atom = $sd_response->sac_state_iri_atom;

// Request the Deposit Receipt from the Edit-IRI.
print "\nRetrieving Deposit Receipt from " . $edit_iri . "\n";
$sd_response = $sword_client->retrieveDepositReceipt($edit_iri, '', '', $on_behalf_of);
if ($sd_response->sac_status == 200) {
    print "OK, received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
}
else {
    print "Received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
    print $sd_response->toString();
}

// Edit the content ().
print "\nEditing the content via Atom entry (" . $atom_entry_modify . ") PUT to " . $edit_iri . "\n";
// We use a try/catch here because the SWORD v2 PHP client library throws an
// error parsing the response from a PUT to the Edit-IRI.
try {
    $sd_response = $sword_client->replaceMetadata($edit_iri, '', '', $on_behalf_of, $atom_entry_modify, false);
}
catch (Exception $e) {
    if ($sd_response->sac_status == 200) {
        print "OK, received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
    }
    else {
        print "Received HTTP status code: " . $sd_response->sac_status . " (" . $sd_response->sac_statusmessage . ")\n";
        print $sd_response->toString(); 
    }
}

print "\nSWORD IRIs....\n";
print "Edit-IRI: $edit_iri\n";
print "Cont-IRI: $cont_iri\n";
print "EM-IRI: $edit_media\n";
print "State-IRI: $statement_atom\n";

?>
