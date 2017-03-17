Source Code
-----------

The source code for LOCKSS-O-Matic is released to the public under the
terms of the `MIT license`. The public `source code`_ is available on
GitHub.

LOCKSS-O-Matic is written in PHP, using the Symfony framework. The
front end uses the Twitter Bootstrap CSS framework. Dependencies are
managed with Composer, and testing is done with PHPUnit.

The source code is organized as a collection of Symfony bundles which
serve specific purposes.

:ref:`CoreBundle <corebundle-label>`

   Provides menuing, a home page, and services which are useful to all
   bundles.

:ref:`CrudBundle <crudbundle-label>`

   Defines most database entities and the controllers, forms, and
   other code necessary for them.

:ref:`LockssBundle <lockssbundle-label>`

   All of the LOCKSS-specific functionality is in this bundle: reading
   and writing the lockss.xml file, titledb.xml files, and
   manifests. It also handles checking the status of the LOCKSS boxes,
   AUs, deposits, and content.

:ref:`LogBundle <logbundle-label>`

   All database actions are logged automatically with this bundle.

:ref:`SwordBundle <swordbundle-label>`

   Content providers communicate with LOCKSS-O-Matic via a subset of
   the SWORD protocol, which is provided by this bundle.

:ref:`UserBundle <userbundle-label>`

   This bundle provides user accounts and permission checking via ACLs.

.. _MIT License: https://opensource.org/licenses/MIT
.. _source code: https://github.com/mjordan/lockss-o-matic

