#!/bin/bash

echo ">> Configuring the Horizon process"

ln -nfs /vagrant/scripts/vm/config/horizon/supervisord.conf /etc/supervisor/conf.d/horizon.conf

supervisorctl reread
supervisorctl reread
