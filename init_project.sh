#!/usr/bin/env bash
composer install
php artisan db:seed
php artisan key:generate
php artisan passport:install
php artisan storage:link
chmod -R 777 resources/
chmod -R 777 config/
chmod -R 777 storage/
chown -R apache resources
chown -R apache storage
chown -R apache config

