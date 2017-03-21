.. _newpln-label:

Creating a new PLN
==================

.. note::

    PLN configuration is complex, and makes heavy use of 
    :ref:`_perms-label`permissions. If you notice permissions 
    issues during testing, consider logging out, clearing the 
    cache, and logging in again.

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

Add Boxes to the PLN
--------------------

Technically, this can be done once the PLN is created. But things have 
and order, and this order was determined by a `topological sort` with 
the most difficult stuff going first.

Once the PLN is created, it is available in the Networks menu in the
top navigation. The PLN has several submenus, including one for Boxes. 
Follow the link to get a list of boxes for the PLN. It should be empty
for a new PLN. Click the Add Box button to start adding a box. 

Enter the box hostname in the field. The protocol should probably be
TCP [#f1]_ but other possiblities may exist. Leave the IP address
blank and LOM will find it out for you. Enter the LOCKSS communication 
port (usually 9729) and the LOCKSS UI/SOAP port (usually 8081) and
select a PLN.

`Lather, rinse, repeat`_.

All done!
---------

At this point, clients should be able to make deposits via the SWORD 
protocol. And LOCKSSOMatic should be able to write out the configuration 
files, manifests, and titledb files.

Wooooo.

.. _topological sort: https://en.wikipedia.org/wiki/Topological_sorting
.. _jarsigner: http://docs.oracle.com/javase/8/docs/technotes/tools/windows/jarsigner.html
.. _Lather, rinse, repeat: https://www.youtube.com/watch?v=UmIiylvLdLc
.. _rubric:: Footnotes

.. [#f1] If you know which other protocols are possible, please let us 
         know.
