#!/usr/bin/env python

# Sample SWORD client for LOCKSS-O-Matic.
#
# Copyright 2014 Mark Jordan.
#
# Distributed under the MIT License, http://opensource.org/licenses/MIT.

import os 
import sys
import requests

server_base_url = 'http://localhost/lockss-o-matic/web/app_dev.php'
sd_iri = '/api/sword/2.0/sd-iri'
col_iri = '/api/sword/2.0/col-iri/'
cont_iri = '/api/sword/2.0/cont-iri/'

# Bail if no SWORD action is provided.
if (len(sys.argv) < 2):
    print "Sorry, you need to provide a SWORD action such as getsd, etc."
    sys.exit()

# Grab the action from the command line.
action = sys.argv[1]
content_provider = sys.argv[2]

# Retrieve Service Document.
if action == 'getSD':
    # Populate the On-Behalf-Of header with the Content Provider's ID.
    headers = {'X-On-Behalf-Of': content_provider}
    print 'Retrieving Service Docuement on behalf of ' + content_provider
    r = requests.get(server_base_url + sd_iri, headers=headers)
    print r.content

# Create a deposit.
# curl -v -H "In-Progress: true" --data-binary @atom_create.xml --request POST http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/col-iri/47
if action == 'createDeposit':
    path_to_atom = sys.argv[3]
    # Populate the On-Behalf-Of header with the Content Provider's ID.
    headers = {'X-In-Progress': False}
    atom = open(path_to_atom, 'rb')
    print 'Creating Deposit on behalf of ' + content_provider
    r = requests.post(server_base_url + col_iri + content_provider, data=atom, headers=headers)
    print r.content
    
# Get SWORD Statement.
# http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/1/1225c695-cfb8-4ebb-aaaa-80da344efa6a/state
if action == 'getStatement':
    content_uuid = sys.argv[3]
    print 'Retrieving Sword Statement on behalf of ' + content_provider
    r = requests.get(server_base_url + cont_iri + content_provider + '/' + content_uuid + '/state')
    print r.content
    
# Modify a deposit.
# curl -v -H "Content-Type: application/xml" -X PUT --data-binary @atom_modify.xml http://localhost/lockss-o-matic/web/app_dev.php/api/sword/2.0/cont-iri/1/1225c695-cfb8-4ebb-aaaa-80da344efa6a/edit
if action == 'modifyDeposit':
    content_uuid = sys.argv[3]
    path_to_atom = sys.argv[4]
    # Populate the On-Behalf-Of header with the Content Provider's ID.
    headers = {'Content-Type': 'application/xml'}
    atom = open(path_to_atom, 'rb')
    print 'Modifying Deposit on behalf of ' + content_provider
    r = requests.put(server_base_url + cont_iri + content_provider + '/' + content_uuid + '/edit', data=atom, headers=headers)
    print r.status_code

