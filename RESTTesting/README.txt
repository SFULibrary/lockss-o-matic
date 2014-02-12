Below are example URLs you can use to test the LOCKSS-O-Matic SWORD server. Note that LOCKSS-O-Matic
does not implement a complete SWORD server. It only implements the minimum functionality required
for a content provider to post a set of content URLs for harvesting by a LOCKSS network, for the
content provider to retrieve s SWORD Statement about those URLs, and to update a 'recrawl' flag
for one or more of those URLs.

Get Service Document
====================

Note that the value of 'On-Behalf-Of' must match a Content Provider ID in the LOCKSS-O-Matic database.

curl -v -H 'On-Behalf-Of: 1' http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/sd-iri

Create resource (i.e., post a set of URLs)
==========================================

Note that the parameter at the end of the URL must match the ID of an existing Content Provider
(typically it is the same as the 'On-Behalf-Of' value used in the Service Document requrest.
The POSTed XML will contain the list of URLs.

curl -v -H "In-Progress: true" --data-binary @create_resource.xml --request POST http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/col-iri/1

Get SWORD statement
===================

The parameter following 'cont-iri' must match the ID of an existing Content Provider. The paramter
preceding '/state' is the UUID of original deposit provided in the "create resource" request.

http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/1/1225c695-cfb8-4ebb-aaaa-80da344efa6a/state


Edit-IRI
========

The parameter following 'cont-iri' must match the ID of an existing Content Provider. The paramter
preceding '/state' is the UUID of original deposit provided in the "create resource" request. The PUTed
XML will contain the list of URLs that are to be flagged as 'recrawl="false"'.

curl -v -H "Content-Type: application/xml" -X PUT --data-binary @edit_resource.xml http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/1/1225c695-cfb8-4ebb-aaaa-80da344efa6a/edit

