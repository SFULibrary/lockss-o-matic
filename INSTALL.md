Overview
========

LOCKSS-O-Matic is still in early stages of development, so there isn't a lot for end users to see. Developers wishing to test the SWORD server can install it as descibed below.

LOCKSS-O-Matic uses the Symfony Web Application Framework (http://symfony.com/). However, you do not need to install Symfony separately.

Installation
============

1) Download https://github.com/mjordan/lockss-o-matic/archive/master.zip and unzip it in your webroot

or

clone the repo at https://github.com/mjordan/lockss-o-matic into your webroot.

2) Make sure the user running your web server needs to have write permissions to the app/cache and app/logs directories. From within the lockss-o-matic directory, issue the following commands:

```sudo chmod -R 777 app/cache```

```sudo chmod -R 777 app/logs```

These commands are the easiest way to allow your web server to write to these directories, but they are also the least secure. You may want to consult the "Setting up Permissions" section of the Symfony documentation at http://symfony.com/doc/current/book/installation.html.

3) Create the database and grant permissions on it:

```mysqladmin -uroot -p create lomtest```

```mysql -uroot -p```

```mysql>grant all on lomtest.* to lomtest@localhost identified by "lomtest";```

4) From within the lockss-o-matic directory, issue the following commands:

```cd app/config```

```cp parameters.yml.dist parameters.yml```

5) Configure the database by editing app/config/paramters.yml: enter the database_name, database_user, and database_password you used in your grant command. Then, from within the lockss-o-matic directory, run:

```php app/console doctrine:schema:update --force```

6) Load the data required to test the SWORD server:

```php app/console doctrine:fixtures:load```

7) Test your PHP configuration by going to the following URL:

http://localhost/lockss-o-matic/web/config.php

You do not need to configure the application. However, if Symfony reports any issues with your PHP configuration, or with file/directory permissions, you should fix those before testing the SWORD server.

8) You are now ready to test the SWORD server as described in RESTTesting/README.txt.
