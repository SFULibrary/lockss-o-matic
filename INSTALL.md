Overview
========

LOCKSS-O-Matic is still in early stages of development, so there isn't a lot for end users to see. Developers or adventursome users can install it as descibed below.

LOCKSS-O-Matic uses the Symfony Web Application Framework (http://symfony.com/). However, you do not need to install Symfony separately.

Installation
============

1) Download https://github.com/mjordan/lockss-o-matic/archive/master.zip and unzip it in your webroot

or

clone the repo at https://github.com/mjordan/lockss-o-matic into your webroot.

2) From within the lockss-o-matic directory, issue the following commands:

```cd app/config```

```cp parameters.yml.dist parameters.yml```

Also, make sure the user running your web server needs to have write permissions to the app/cache and app/logs directories.

3) Test your PHP configuration by going to the following URL:

http://localhost/lockss-o-matic/web/config.php

4) After addressing any issues identified in step 3, test the installation by going to the followint URL:

http://localhost/lockss-o-matic/web/app_dev.php/demo/hello/LOCKSS-O-Matic

You should see the standard Symfony test page.
