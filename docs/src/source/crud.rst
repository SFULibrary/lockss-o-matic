.. _crudbundle-label:

CrudBundle
==========

The CrudBundle manages most entities in LOCKSS-O-Matic, and provides
the necessary controllers, forms, and resources.

Entities
--------

AU

  A LOCKSS Archival Unit, a collection of content items organized by
  size in LOCKSS-O-Matic.

AU Property

  An Archival Unit property is a key-value pair associated with an
  AU. They are organized as trees.

AU Status

  Historical status checks on an AU against all of the boxes.

Box

  A LOCKSS network consists of multiple boxes running the LOCKSS
  daemon. LOCKSS-O-Matic will regularly communicate with each box to
  check on its status and the status of the content it has
  preserved. The box will also communicate with LOCKSS to update its
  configuration.

Box Status

  Historical status checks on the box, checking on its connectivity.

Cache Status

  Each box contains one or more caches storing the preserved
  content. A cache status reports on the cache size and remaining
  space.

Content

  A content item is a single URL in a deposit.

Content Owner

  A content owner is an organization, comprised of multiple users.

Content Property

  Content properties are key-value pairs associated with a content
  item. They may lists or trees.

Content Provider

  A content provider is an external application which sends deposits
  to LOCKSS-O-Matic.

Deposit

  A deposit is one or more content items (URLs and some metadata).

Deposit Status

  Historical record of the status of a deposit in the boxes.

Keystore

  A LOCKSS keystore holds the public keys used to sign LOCKSS plugins
  (jar files).

Pln

  A Private Lockss Network is a collection of boxes and the properties
  used to define the PLN in a lockss.xml file.

Plugin

  A LOCKSS plugin.jar file, signed iwth a key in the keystore and made
  available to one or more Plns.

Plugin Property

  Plugin properties are key-value properties, and may be
  hierarchical. 

Services
--------

Some reusable components are defined as Symfony services, for use iwth
the dependency injection system (the "container").

AuBuilder

  Creates archival units (AUs) from the metadata associated with a
  content item.

AuIdGenerator

  Given an AU or a content item, generates the LOCKSS AuId by
  combining the definitional "ConfigParamDescrs" from the AU's plugin
  definition.

AuPropertyGenerator

  Builds one or more AuProperty entities for an AU. LOCKSS AU
  properties can be defined as vsprintf-style format strings with some
  associated data.

ContentBuilder

  Creates a content entity from either a piece of SWORD deposit XML or
  from some form data.

DepositBuilder

  Builds a deposit entity from either a SWORD deposit XML or from some
  deposit data.
