Overview
========

LOCKSS-O-Matic is still in early stages of development, so there isn't a lot for end users to see. Developers or adventursome users can install it as descibed below.

LOCKSS-O-Matic uses the Symfony Web Application Framework (http://symfony.com/). However, you do not need to install Symfony separately.

Installation
============

1) Download https://github.com/mjordan/lockss-o-matic/archive/master.zip and unzip it in your webroot

or

clone the repo at https://github.com/mjordan/lockss-o-matic into your webroot.

2) Make sure the user running your web server needs to have write permissions to the app/cache and app/logs directories. From within the lockss-o-matic directory, issue the following commands:

```sudo chmod -R app/cache```

```sudo chmod -R app/logs```

These commands are the easiest way to allow your web server to write to these directories, but they are also the least secure. You may want to consult the "Setting up Permissions" section of the Symfony documentation at http://symfony.com/doc/current/book/installation.html.

3) Create the database and grant permissions on it:

```mysqladmin -uroot -p create lomtest```

```mysql -uroot -p```

```mysql>grant all on lomtest.* to lomtest@localhost identified by "lomtest";```

4) From within the lockss-o-matic directory, issue the following commands:

```cd app/config```

```cp parameters.yml.dist parameters.yml```

5) Configure the database

Edit app/config/paramters.yml and enter the database_name, database_user, and database_password you used in your grant command.

From within the lockss-o-matic directory, run:

```php app/console doctrine:schema:update --force```

6) Test your PHP configuration by going to the following URL:

http://localhost/lockss-o-matic/web/config.php

7) After addressing any issues identified in step 3, test the installation by going to the followint URL:

http://localhost/lockss-o-matic/web/app_dev.php/demo/hello/LOCKSS-O-Matic

You should see the standard Symfony test page.
