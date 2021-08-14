#!/bin/sh

cd $1
composer update
composer install
touch $1"/database/database.sqlite"
php artisan key:generate
php artisan migrate:fresh --seed
php artisan optimize
chown -R www-data:www-data $1

exit 0
