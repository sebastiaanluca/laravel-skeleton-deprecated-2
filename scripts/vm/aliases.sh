#!/bin/bash

export EDITOR=/usr/bin/nano

`set completion-ignore-case on` to /etc/inputrc

# Directories

alias ll='ls -FGlAhp'
alias ..='cd ../'
alias ...='cd ../../'
alias ....='cd ../../../'
alias .....='cd ../../../../'

alias df='df -h'
alias diskusage='df'
alias fu='du -ch'
alias folderusage='fu'
alias tfu='du -sh'
alias totalfolderusage='tfu'

# Development

alias c='composer'
alias cr='composer require'
alias cdump='composer dumpautoload'
alias cda='cdump'
alias coutdated='composer outdated --direct'
alias co='coutdated'
alias update-global-composer='cd ~/.composer && composer update'
alias composer-update-global='update-global-composer'

alias a='artisan'
alias art='artisan'
alias arti='artisan'
alias pa='artisan'
alias phpa='artisan'

alias y='yarn'
alias yr='yarn run'

alias test='vendor/bin/phpunit'

alias xdebugoff='sudo phpdismod -s cli xdebug'
alias xdebugon='sudo phpenmod -s cli xdebug'

alias es-list-indices='curl -XGET http://localhost:9200/_cat/indices?v'

# Misc

alias force-upgrade='DEBIAN_FRONTEND=noninteractive sudo apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" -y upgrade'
alias clean-packages='sudo apt-get -y autoremove && sudo apt-get -y autoclean'

alias localip='ifconfig | grep -Eo ''inet (addr:)?([0-9]*\.){3}[0-9]*'' | grep -Eo ''([0-9]*\.){3}[0-9]*'' | grep -v ''127.0.0.1'''
alias ip='curl icanhazip.com'
alias ports='netstat -tulanp'
alias reset-network='sudo service network-manager restart'

alias meminfo='free -m -l -t'
alias memtop10='ps auxf | sort -nr -k 4 | head -10'
alias cputop10='ps auxf | sort -nr -k 3 | head -10'

alias copy='rsync -avv --stats --human-readable --itemize-changes --progress --partial'

# Functions

mkcdir ()
{
    mkdir -p -- "$1" &&
    cd -P -- "$1"
}

artisan () {
    php artisan "$@"
}

routes () {
    if [ $# -eq 0 ]
    then
        php artisan route:list
    else
        php artisan route:list | grep --color ${1}
    fi
}

es-delete-index () {
    curl -XDELETE localhost:9200/$1?pretty=true
}

es-list-documents () {
    curl -XGET localhost:9200/$1/_search?pretty=true&q=*:*
}

count() {
    DIR=$([[ ! -z "$1" ]] && echo $1 || echo $PWD)
    
    COUNT_DIRS=$(find $1 -mindepth 1 -maxdepth 1 -type d -printf x | wc -c)
    COUNT_FILES=$(find $1 -mindepth 1 -maxdepth 1 -type f -printf x | wc -c)
    
    echo "$DIR contains $COUNT_DIRS directories and $COUNT_FILES files"
}

count-recursively() {
    COUNT=`(tree $1 | tail -1)`
    DIR=$([[ ! -z "$1" ]] && echo $1 || echo $PWD)
    
    echo "$DIR contains $COUNT files and folders"
}

## Publicly share VM
share () {
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
