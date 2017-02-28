#!/bin/bash

alias ..="cd .."
alias ...="cd ../.."

alias c='composer'
alias co='composer'
alias cr='composer require'
alias cda='composer dumpautoload'
alias a=artisan
alias art=artisan
alias arti=artisan
alias pa=artisan
alias phpa=artisan
alias y='yarn'
alias yr='yarn run'

alias test='vendor/bin/phpunit'
alias phpspec='vendor/bin/phpspec'
alias phpunit='vendor/bin/phpunit'
alias serve=serve-laravel

alias xoff='sudo phpdismod -s cli xdebug'
alias xon='sudo phpenmod -s cli xdebug'

function artisan() {
    php artisan "$@"
}

# Elasticsearch

alias es-list-indices='curl -XGET http://localhost:9200/_cat/indices?v'

es-delete-index() {
    curl -XDELETE localhost:9200/$1?pretty=true
}

es-list-documents() {
    curl -XGET localhost:9200/$1/_search?pretty=true&q=*:*
}

# Publicly share VM

function share() {
    if [[ "$1" ]]
    then
        ngrok http -host-header="$1" 80
    else
        echo "Error: missing required parameters."
        echo "Usage: "
        echo "  share domain"
    fi
}

# Automatically navigate into project dir on session start
cd /vagrant
