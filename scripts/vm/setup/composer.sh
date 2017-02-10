#!/bin/bash

echo ">> Installing Composer packages"

composer install --no-scripts
composer run-script post-update-cmd
