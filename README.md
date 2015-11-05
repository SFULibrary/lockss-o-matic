Overview
========

LOCKSS-O-Matic helps automate the ingestion of content into a
[Private LOCKSS Network](http://www.lockss.org/community/networks/) (PLN).
It also monitors the PLN to ensure that all member boxes are online and
operating normally.

The LOCKSS-O-Matic web application acts as the "admin server" for the PLN,
which means that the LOCKSS boxes in the network use it as the source for the
configuration files that govern what content they harvest and preserve.
LOCKSS-O-Matic derives these configration files from its interaction with
other applications (known as content providers) or from humans registering
content to be preserved using a set of in-browser tools. LOCKSS-O-Matic
implements the [SWORD](http://swordapp.org/) protocol to communicate with
content providers. The first two content providers will
be [Archivematica](https://www.archivematica.org) and
[Open Journal Systems](http://pkp.sfu.ca/ojs/) but LOCKSS-O-Matic can be used
with any content management system or other type of application that produces
content to be preserved in a LOCKSS network.

Networks
========

LOCKSS-O-Matic does not deploy LOCKSS networks - currently that must be done by
system administrators. However, LOCKSS-O-Matic will provide tools to generate
configuration files for the boxes in the network. Once a network is operational,
getting content into it will be as easy as registering a list of URLs to harvest,
provided they are compatible with the included LOCKSS plugins (which, initially,
means that the files are compressed archives such as ZIP, 7z, or tgz formats).
 types of content will be possible with the appropraite LOCKSS plugins.
Archivematica will include a plugin to enable integration with LOCKSS-O-Matic.

Roadmap
=======

LOCKSS-O-Matic is being developed primarily at
Simon Fraser University Library](http://www.lib.sfu.ca/). Development has
already begun and early versions of LOCKSS-O-Matic should be available for
testing in fall of 2015.
[Details](https://github.com/mjordan/lockss-o-matic/wiki/Roadmap) are available
on the project wiki.

If you would like to participate in testing or development, please contact
mjordan - sfu.ca.


UserBundle
==========

The user bundle has several external dependencies:

 * FOSUserBundle: https://github.com/FriendsOfSymfony/FOSUserBundle
 * ProblematicAclBundle: https://github.com/Problematic/ProblematicAclManagerBundle

Both of these are installed via `composer update` and shouldn't require any
configuration after the update. Once they are installed, `php app/console doctrine:schema:update --force` can
be used to create the user and ACL tables in the database.

There are sample users which can be added to the database:

```
    php app/console doctrine:fixtures:load --append --fixtures=src/LOCKSSOMatic/UserBundle/DataFixtures/ORM
```

This will not install any ACL data, as it would require fore-knowledge of the entities
in the database. Note the use of `--append` to prevent any data loss. After installing
the sample users, visit /login to login as admin@example.com with password supersecret.

The UserBundle also comes with some tests for use with PHPUnit.

```
phpunit -c app src/LOCKSSOMatic/UserBundle/Tests
```

will run them.
