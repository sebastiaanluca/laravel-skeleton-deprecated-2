#!/bin/bash

echo ">> Configuring the Horizon process"

sudo ln -nfs /vagrant/scripts/vm/config/horizon/supervisord.conf /etc/supervisor/conf.d/horizon.conf

sudo supervisorctl reread
sudo supervisorctl update
