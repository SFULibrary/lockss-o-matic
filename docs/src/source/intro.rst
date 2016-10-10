Introduction
============

The LOCKSS-O-Matic web application acts as the "admin server" for the
PLN, which means that the LOCKSS boxes in the network use it as the
source for the configuration files that govern what content they
harvest and preserve. LOCKSS-O-Matic derives these configration files
from its interaction with other applications (known as content
providers) or from humans registering content to be preserved using a
set of in-browser tools. LOCKSS-O-Matic implements the `SWORD`_
protocol to communicate with content providers. The first two content
providers will be `Archivematica`_ and `Open Journal Systems`_ but
LOCKSS-O-Matic can be used with any content management system or other
type of application that produces content to be preserved in a LOCKSS
network.


Networks
--------

LOCKSS-O-Matic does not deploy LOCKSS networks - currently that must
be done by system administrators. However, LOCKSS-O-Matic will provide
tools to generate configuration files for the boxes in the
network. Once a network is operational, getting content into it will
be as easy as registering a list of URLs to harvest, provided they are
compatible with the included LOCKSS plugins (which, initially, means
that the files are compressed archives such as ZIP, 7z, or tgz
formats). types of content will be possible with the appropraite
LOCKSS plugins. Archivematica will include a plugin to enable
integration with LOCKSS-O-Matic.

Development
-----------

LOCKSS-O-Matic is developed at `Simon Fraser University
Library`_. If you would like to participate in testing or development,
please contact `Mark Jordan`_.

.. _SWORD: http://swordapp.org/
.. _Archivematica: https://www.archivematica.org/
.. _Open Journal Systems: http://pkp.sfu.ca/ojs/
.. _Mark Jordan: https://www.github.com/mjordan/
.. _Simon Fraser University Library: https://lib.sfu.ca
