#!/bin/bash

cd /home/ventreminex

rm -R /vendor
rm -R composer.lock

composer install --no-dev --no-progress -a
composer update --no-dev --no-progress -a
composer upgrade --no-dev --no-progress -a
composer do -o --no-dev --no-progress -a

service nginx reload