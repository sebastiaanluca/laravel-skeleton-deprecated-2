#!/bin/bash

echo ">> Setting up database"

php artisan migrate
php artisan db:seed
