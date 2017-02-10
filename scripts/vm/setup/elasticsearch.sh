#!/bin/bash

echo ">> Installing and setting up Elastic Search"

# Download and install the public signing key
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch --no-verbose | sudo apt-key add -

# You may need to install the apt-transport-https package on Debian before proceeding
sudo DEBIAN_FRONTEND=noninteractive apt-get install apt-transport-https

# Save the repository definition to /etc/apt/sources.list.d/elastic-5.x.list
if [ ! -f "/etc/apt/sources.list.d/elastic-5.x.list" ];then
    echo "deb https://artifacts.elastic.co/packages/5.x/apt stable main" | sudo tee -a /etc/apt/sources.list.d/elastic-5.x.list
fi

# Update sources and install
sudo DEBIAN_FRONTEND=noninteractive apt-get update -y --allow-unauthenticated
sudo DEBIAN_FRONTEND=noninteractive apt-get install elasticsearch -y --allow-unauthenticated

# Start Elasticsearch
sudo systemctl daemon-reload
sudo systemctl enable elasticsearch.service
sudo systemctl start elasticsearch.service
