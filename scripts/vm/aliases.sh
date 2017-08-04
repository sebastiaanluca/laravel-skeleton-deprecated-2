#!/bin/bash

export EDITOR=/usr/bin/nano

# Directories

alias ..='cd ../'
alias ...='cd ../../'
alias ....='cd ../../../'
alias .....='cd ../../../../'

alias h='cd ~'
alias home='h'
alias ll='ls -FGlAhp'
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
alias phpspec='vendor/bin/phpspec'

alias xdebugoff='sudo phpdismod -s cli xdebug'
alias xdebugon='sudo phpenmod -s cli xdebug'

alias es-list-indices='curl -XGET http://localhost:9200/_cat/indices?v'

alias serve=serve-laravel

# Misc

alias force-upgrade='DEBIAN_FRONTEND=noninteractive sudo apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" -y upgrade'
alias clean-packages='sudo apt-get -y autoremove && sudo apt-get -y autoclean'

alias localip="ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1'"
alias ip='curl icanhazip.com'
alias ports='netstat -tulanp'
alias reset-network='sudo service network-manager restart'

alias meminfo='free -m -l -t'
alias memtop10='ps auxf | sort -nr -k 4 | head -10'
alias cputop10='ps auxf | sort -nr -k 3 | head -10'

alias copy='rsync -avv --stats --human-readable --itemize-changes --progress --partial'

# Functions

function mkcdir ()
{
    mkdir -p -- "$1" &&
    cd -P -- "$1"
}

function artisan () {
    php artisan "$@"
}

function routes () {
    if [ $# -eq 0 ]
    then
        php artisan route:list
    else
        php artisan route:list | grep --color ${1}
    fi
}

function dusk () {
    pids=$(pidof /usr/bin/Xvfb)

    if [ ! -n "$pids" ]; then
        Xvfb :0 -screen 0 1280x960x24 &
    fi

    php artisan dusk "$@"
}

function es-delete-index () {
    curl -XDELETE localhost:9200/$1?pretty=true
}

function es-list-documents () {
    curl -XGET localhost:9200/$1/_search?pretty=true&q=*:*
}

function count () {
    DIR=$([[ ! -z "$1" ]] && echo $1 || echo $PWD)
    
    COUNT_DIRS=$(find $1 -mindepth 1 -maxdepth 1 -type d -printf x | wc -c)
    COUNT_FILES=$(find $1 -mindepth 1 -maxdepth 1 -type f -printf x | wc -c)
    
    echo "$DIR contains $COUNT_DIRS directories and $COUNT_FILES files"
}

function count-recursively () {
    COUNT=`(tree $1 | tail -1)`
    DIR=$([[ ! -z "$1" ]] && echo $1 || echo $PWD)
    
    echo "$DIR contains $COUNT files and folders"
}

function serve-laravel () {
    if [[ "$1" && "$2" ]]
    then
        sudo bash /vagrant/vendor/laravel/homestead/scripts/create-certificate.sh "$1"
        sudo dos2unix /vagrant/vendor/laravel/homestead/scripts/serve-laravel.sh
        sudo bash /vagrant/vendor/laravel/homestead/scripts/serve-laravel.sh "$1" "$2" 80
    else
        echo "Error: missing required parameters."
        echo "Usage: "
        echo "  serve domain path"
    fi
}

function share () {
    if [[ "$1" ]]
    then
        ngrok http ${@:2} -host-header="$1" 80
    else
        echo "Error: missing required parameters."
        echo "Usage: "
        echo "  share domain"
        echo "Invocation with extra params passed directly to ngrok"
        echo "  share domain -region=eu -subdomain=test1234"
    fi
}

function __has_pv () {
    $(hash pv 2>/dev/null);

    return $?
}

function __pv_install_message () {
    if ! __has_pv; then
        echo $1
        echo "Install pv with \`sudo apt-get install -y pv\` then run this command again."
        echo ""
    fi
}

function dbexport () {
    FILE=${1:-/vagrant/mysqldump.sql.gz}

    # This gives an estimate of the size of the SQL file
    # It appears that 80% is a good approximation of
    # the ratio of estimated size to actual size
    SIZE_QUERY="select ceil(sum(data_length) * 0.8) as size from information_schema.TABLES"

    __pv_install_message "Want to see export progress?"

    echo "Exporting databases to '$FILE'"

    if __has_pv; then
        ADJUSTED_SIZE=$(mysql --vertical -uhomestead -psecret -e "$SIZE_QUERY" 2>/dev/null | grep 'size' | awk '{print $2}')
        HUMAN_READABLE_SIZE=$(numfmt --to=iec-i --suffix=B --format="%.3f" $ADJUSTED_SIZE)

        echo "Estimated uncompressed size: $HUMAN_READABLE_SIZE"
        mysqldump -uhomestead -psecret --all-databases --skip-lock-tables 2>/dev/null | pv  --size=$ADJUSTED_SIZE | gzip > "$FILE"
    else
        mysqldump -uhomestead -psecret --all-databases --skip-lock-tables 2>/dev/null | gzip > "$FILE"
    fi

    echo "Done."
}

function dbimport () {
    FILE=${1:-/vagrant/mysqldump.sql.gz}

    __pv_install_message "Want to see import progress?"

    echo "Importing databases from '$FILE'"

    if __has_pv; then
        pv "$FILE" --progress --eta | zcat | mysql -uhomestead -psecret 2>/dev/null
    else
        cat "$FILE" | zcat | mysql -uhomestead -psecret 2>/dev/null
    fi

    echo "Done."
}

# Automatically navigate into project dir on session start
cd /vagrant
