#!/bin/bash

if [ ! -f ".env" ];then
    echo ">> Copying example .env file"
    cp .env.example .env
    
    echo ">> Generating unique application key"
    php artisan key:generate
fi
