#!/bin/bash

echo ">> Installing and setting up Minio local S3 server"

wget -0 /usr/bin/minio https://dl.minio.io/server/minio/release/linux-amd64/minio

chmod +x /usr/bin/minio
cp -v /vagrant/scripts/vm/config/minio/minio.json ~/.minio/config.json

ln -nfs /vagrant/scripts/vm/config/minio/supervisord /etc/supervisor/conf.d/minio.conf

supervisorctl reread
supervisorctl reread
