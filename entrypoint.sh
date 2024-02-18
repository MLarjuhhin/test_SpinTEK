#!/bin/sh
# entrypoint.sh

#create folders storage|log if not exists
mkdir -p /var/www/public/spin/storage
mkdir -p /var/www/public/spin/log


#set users
chown -R www-data:www-data /var/www/public/spin/storage
chown -R www-data:www-data /var/www/public/spin/log

#set permissons
chmod -R 775 /var/www/public/spin/storage
chmod -R 775 /var/www/public/spin/log


#run composer install
cd /var/www/public/spin
composer install



#run php
php-fpm

exec "$@"
