#!/bin/sh

cd $1
php artisan schedule:list >> /dev/null
php artisan schedule:work >> /dev/null &

exit 0
