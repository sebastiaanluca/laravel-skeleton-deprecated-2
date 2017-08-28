#!/bin/bash

bash /vagrant/scripts/vm/extras/gtop.sh
bash /vagrant/scripts/vm/extras/prestissimo.sh
bash /vagrant/scripts/vm/extras/shell.sh

# Byobu should always be installed as last since it stops following scripts
bash /vagrant/scripts/vm/extras/byobu.sh

clear
