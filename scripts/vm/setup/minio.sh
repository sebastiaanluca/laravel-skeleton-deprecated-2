#!/bin/bash

echo ">> Installing and setting up Minio local S3 server"

# Server

sudo wget -O /usr/bin/minio https://dl.minio.io/server/minio/release/linux-amd64/minio --no-verbose
sudo chmod +x /usr/bin/minio

mkdir -p ~/.minio
ln -nfs /vagrant/scripts/vm/config/minio/config.json ~/.minio/config.json

sudo ln -nfs /vagrant/scripts/vm/config/minio/supervisord.conf /etc/supervisor/conf.d/minio.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start minio

# Client

sudo wget -O /usr/bin/mc https://dl.minio.io/client/mc/release/linux-amd64/mc --no-verbose
sudo chmod +x /usr/bin/mc

mkdir -p ~/.mc
ln -nfs /vagrant/scripts/vm/config/mc/config.json ~/.mc/config.json

# Create a default bucket on our minio S3 storage server and
# allow viewing files, but disable listing entire directories

mc mb -p local/default
mc policy download local/default/*
