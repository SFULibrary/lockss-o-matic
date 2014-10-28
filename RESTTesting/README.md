Testing the LOCKSS-O-Matic SWORD server using Curl
==================================================

Below are example URLs you can use to test the LOCKSS-O-Matic SWORD server. Note that LOCKSS-O-Matic
does not implement a complete SWORD server. It only implements the minimum functionality required
for a content provider to post a set of content URLs for harvesting by a LOCKSS network, for the
content provider to retrieve a SWORD Statement about those URLs, and to update a 'recrawl' flag
for one or more of those URLs.

Get Service Document
====================

Note that the value of 'X-On-Behalf-Of' must match a Content Provider UUID in the 
LOCKSS-O-Matic database. If you install the data fixtures via `php app/console doctrine:fixtures:load`
there will be a content provider with UUID f705d95b-7a0a-451c-a51e-242bc4ab53cf

curl -v -H 'X-On-Behalf-Of: f705d95b-7a0a-451c-a51e-242bc4ab53cf' http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/sd-iri

Create resource (i.e., post a set of URLs)
==========================================

Note that the parameter at the end of the URL must match the ID of an existing Content Provider
(typically it is the same as the 'On-Behalf-Of' value used in the Service Document requrest.
The POSTed XML will contain the list of URLs.

curl -v -H "In-Progress: true" --data-binary @atom_create.xml --request POST http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/col-iri/f705d95b-7a0a-451c-a51e-242bc4ab53cf

Get SWORD statement
===================

The parameter following 'cont-iri' must match the ID of an existing Content Provider. The paramter
preceding '/state' is the UUID of original deposit provided in the "create resource" request.

http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/f705d95b-7a0a-451c-a51e-242bc4ab53cf/1225c695-cfb8-4ebb-aaaa-80da344efa6a/state


Edit-IRI
========

The parameter following 'cont-iri' must match the ID of an existing Content Provider. The paramter
preceding '/edit' is the UUID of original deposit provided in the "create resource" request. The PUTed
XML will contain the list of URLs that are to be flagged as 'recrawl="false"'.

curl -v -H "Content-Type: application/xml" -X PUT --data-binary @atom_modify.xml 
http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/f705d95b-7a0a-451c-a51e-242bc4ab53cf/1225c695-cfb8-4ebb-aaaa-80da344efa6a/edit

==============================================================
Testing the LOCKSS-O-Matic SWORD server using a client library
==============================================================

You can also test the SWORD server with sample_sword_client.php, which uses the SWORD v2 PHP
client library (https://github.com/swordapp/swordappv2-php-library, not distributed with
LOCKSS-O-Matic). Download or clone the library, modify the first three variables in the client
script,* and then run 'php sample_sword_client.php'.

* If you are testing LOCKSS-O-Matic for the first time, and you clone the SWORD library into
the RESTTesting directory, you won't even need to modify these variables, running 'php 
sample_sword_client.php' should work out of the box.
