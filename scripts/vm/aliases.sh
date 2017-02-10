#!/bin/bash

# Aliases
alias c='composer'
alias co='composer'
alias cr='composer require'
alias cda='composer dumpautoload'
alias a='php artisan'
alias art='php artisan'
alias arti='php artisan'
alias pa='php artisan'
alias phpa='php artisan'
alias y='yarn'
alias yr='yarn run'
alias test='vendor/bin/phpunit'

alias es-list-indices='curl -XGET http://localhost:9200/_cat/indices?v'

es-delete-index() {
    curl -XDELETE localhost:9200/$1?pretty=true
}

es-list-documents() {
    curl -XGET localhost:9200/$1/_search?pretty=true&q=*:*
}

# Automatically navigate into project dir on session start
cd /vagrant
