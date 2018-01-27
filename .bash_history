pwd
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento setup:static-content:deploy -f
w
su -
pwd
php bin/magento setup:static-content:deploy -f
php bin/magneto cache:status
php bin/magento cache:status
php bin/magento cache:disable
pwd
rm -rf var/* pub/static/* 
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f en_US en_GB
rm -rf var/* pub/static/* 
php bin/magento setup:static-content:deploy -f en_US en_GB
pwd
rm -rf var/* pub/static/* 
php bin/magento setup:static-content:deploy -f en_US en_GB
chmod -R 7777 pub/static var/
rm -rf var/* pub/static/* 
php bin/magento setup:static-content:deploy -f en_US en_GB
pwd
php bin/magento deploy:mode:show
chmod -R 777 var pub/static
pwd
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy
php bin/magento setup:static-content:deploy -f
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy
php bin/magento setup:static-content:deploy -f
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy -f
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy -f
chmod -R 777 var pub/static
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy  -f
chmod -R 777 var pub/static
rm -rf pub/static/*
rm -rf var/*
php bin/magento setup:upgrade
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:upgrade
php bin/magento indexer:reindex
rm -rf var/*
rm -rf pub/static/*
php bin/magento setup:static-content:deploy  -f
chmod -R 777 var pub/static
pwd
ll
clear
git status
git add generated/.htaccess
git add app/design/frontend/Babystyle/Theme/Magento_Theme/templates/html/title.phtml
git commit -m update "title file"
clear
git status
git pull
git stash
git config --global user.email "allin.akumar@gmail.com"
git config --global user.name "allininfosystems"
git stash
git pull
rm -rf var/* pub/static/*
rm -rf generated/*
rm -rf var/* pub/static/* generated/*
php bin/magento setup:static-content:deploy en_CA en_US -f
pwd
git status
git add app/etc/env.php
git commit -m "update env.php"
git push
git pull
php bin/magento setup:static-content:deploy en_CA en_US -f
rm -rf var/* pub/static/* generated/*
php bin/magento setup:static-content:deploy en_CA en_US -f
chmod -R 777 var pub/static
php bin/magento cache:clean
php bin/magento cache:flush
php bin/magento indexer:reindex
php bin/magento indexer:reindex
chmod -R 777 generated
chmod -R 777 var pub/static/ generated
