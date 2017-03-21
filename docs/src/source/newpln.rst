.. _newpln-label:

Creating a new PLN
==================

Set up a content provider
--------------------------

(describe a provider here).

Configure a PLN
---------------

The the form will ask for a username and password. They
are the credentials that LOCKSSOMatic will use to communicate with
the LOCKSS daemons via SOAP.

Make sure you write the password down - it will be unrecoverable.

.. todo:: 

    The password *should* be unrecoverable. It should be saved 
    as a SHA1 hash, not plain text. But it is. Sigh. Bug.

At this point the PLN won't be functional, so keep going.

Add a Keystore (optional)
-------------------------

Plugins which are not included in the LOCKSS distribution must 
be signed with the Java `jarsigner`_ tools. There's a whole 
complex process, which involves creating a public/private key
pair, using the private key to sign the plugin .jar file, and 
then adding the public key to LOCKSS (via LOCKSSOMatic) so that
the public key can be used to verify the signature on the plugin.

There is a Keystore button on the Pln page. Click it, and you 
will be asked to upload a keystore file.

LOCKSSOMatic will happily accept plugins without a keystore. It 
only needs to read the XML inside the plugin.jar file. LOCKSS
will refuse to use the plugin without the keystore file.

Add one or more Plugins
-----------------------

Even if your deposits to LOCKSS will be using plugins from LOCKSS,
LOCKSSOMatic must know about the plugins. The plugins define how
LOCKSS expects things like the manifest files and URLs.

If you're using a LOCKSS plugin, you'll need to dig out the .jar
file. Otherwise, you should be using the plugin's .jar file.

There is a Plugins button on the Pln page. You can click it, but it
will not let you upload a plugin. Bummer. There is a poorly named
"Edit plugins for this PLN" link, but it only lets you select which
plugin that has already been added to LOCKSSOMatic will be used.

.. todo::

    The Pln Plugin page should have a form to upload plugins just
    like the Keystore has a page to upload a keystore file.

Plugins can be added to LOCKSSOMatic via the command line like so:

.. code-block:: shell

    $ ./app/console lom:import:plugin path/to/plugin.jar

You can add as many plugins as you'd like with one command.

Once the plugin is added to LOCKSSOMatic, it must be added to the
PLN by associating it with a content provider. 

.. note::

    Different PLNs can be associated with different versions
    of the same plugin. Upload multiple versinos of a plugin with the
    same FQDN, then click the Plugins button on the Pln page, then 
    click the Edit Plugins link. You will be given a list of plugins 
    sorted by FQDN, and showing version numbers. This poorly named link
    should probably be "Edit Plugins For This PLN."

    This will only work for plugins that have been associated with a 
    content provider first.

.. todo::

    Rename the poorly named "Edit Plugins" link. And really, there
    should be an easier way to do all of this.


Create a Content Provider
-------------------------

Select Providers from the main navigation menu. This will bring you
to a list of content providers. There's a button to add a 
content provider. Do the thing.

.. todo::

    Does the deposit button on the ContentProvider page actually work?
    I don't remember the last time I tried it.

.. todo::

    Now that you have created a Content Provider, you should be able to
    view the boxes and stuff. But that doesn't seem to be working 
    due to permissions issues. Sigh.

.. _jarsigner: http://docs.oracle.com/javase/8/docs/technotes/tools/windows/jarsigner.html