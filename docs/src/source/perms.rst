.. _perms-label:

LOCKSSOMatic Permissions
========================

LOCKSSOMatic permissions are complex, and rely on Symfony and PHP sessions 
working together, and have surprising interactions with the caching layer.

If there are strange permission issues, try logging out and clearing the 
Symfony cache. The logout link is in the user menu, inside the top navigation
under the user name.

The cache can be cleared at the command line.

.. code-block:: shell

    $ ./app/console cache:clear

It is especially important to clear the cache after updating the code.