#!/bin/bash

echo "> Provisioning server"

sudo su vagrant

cd /vagrant

bash /vagrant/scripts/vm/setup.sh
bash /vagrant/scripts/vm/extras.sh
