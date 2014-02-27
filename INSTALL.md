Overview
========

LOCKSS-O-Matic is still in early stages of development, so there isn't a lot for end users to see. Developers wishing to test the SWORD server can install it as descibed below.

LOCKSS-O-Matic uses the Symfony Web Application Framework (http://symfony.com/). However, you do not need to install Symfony separately.

Prerequisites
=============

Details are available at http://symfony.com/doc/current/reference/requirements.html, but in a nutshell, you need PHP 5.3.3 or higher and MySQL to be installed. Also, your php.ini file needs to have its ```date.timezone``` setting defined. Support for other databases will be added soon.

Installation
============

1) Create a database for LOCKSS-O-Matic and grant permissions on it:

```mysqladmin -uroot -p create lomtest```

```mysql -uroot -p```

```mysql>grant all on lomtest.* to lomtest@localhost identified by "lomtest";```

2) Download https://github.com/mjordan/lockss-o-matic/archive/master.zip and unzip it in your webroot

or

clone the repo at https://github.com/mjordan/lockss-o-matic into your webroot.

3) Update your database connection details. From within the lockss-o-matic directory, issue the following commands:
  
  ```cd app/config```
  
  ```cp parameters.yml.dist parameters.yml```

Edit parameters.yml so that the database_name, database_user, and database_password values match those you used in step 1.

4) Update your site settings. From within the same directory you entered in step 3 (app/config), issue the following command:
  
  ```cp LOMSettings.yml.dist LOMSettings.yml```

Edit LOMSettings.yml so that it contains the correct base_url and path_to_event_log values for the computer you are installing LOCKSS-O-Matic on (the others are not important for now).

5) Install Composer, which is the standard tool for managing Symfony applications.

From within the lockss-o-matic directory, issue the following command:

```curl -s https://getcomposer.org/installer | php```

6) Install LOCKSS-O-Matic's external libraries. From withing the lockss-o-matic directory, issue the following command:

```php composer.phar install```

7) Make sure the user running your web server needs to have write permissions to the app/cache and app/logs directories. From within the lockss-o-matic directory, issue the following commands:

```sudo chmod -R 777 app/cache```

```sudo chmod -R 777 app/logs```

These commands are the easiest way to allow your web server to write to these directories, but they are also the least secure. You may want to consult the "Setting up Permissions" section of the Symfony documentation at http://symfony.com/doc/current/book/installation.html.

8) Create the LOCKSS-O-Matic database tables. From within the lockss-o-matic directory, run:

```php app/console doctrine:schema:update --force```

9) Load the data required to test the SWORD server. From within the lockss-o-matic directory, run:

```php app/console doctrine:fixtures:load```

Answer 'y' if asked if it is OK to purge the database

10) Test your PHP configuration by going to the following URL:

http://localhost/lockss-o-matic/web/config.php

You do not need to configure the application. However, if Symfony reports any issues with your PHP configuration, or with file/directory permissions, you should fix those before testing the SWORD server. If you change your PHP configuration, don't forget to restart your web server.

11) You are now ready to test the SWORD server as described in RESTTesting/README.txt.
