#!/bin/bash

echo ">> Linking public/storage to storage/app/public"

php artisan storage:link
