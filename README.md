# MyKeep - OpenSource alternative to Google Keep
=============================================

<img src="https://github.com/sampobek/mykeep/blob/master/web/apple-touch-icon.png?raw=true" width="250" align="left" />

MyKeep is an open-source, self-hosted alternative to Google Keep ®.

## Run your own MyKeep

Installing instructions

You need PHP, Apache2, MySql, Git, Composer, Bower to be installed.

```
cd ~
mkdir www
cd www/
git clone https://github.com/sampobek/mykeep.git
cd mykeep/
composer install
```
Configure database and smtp
```
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console assets:install --symlink
bin/console fos:js-routing:dump --target=web/js/routes.js

bower install
```

Visit http://127.0.0.1:8000/
