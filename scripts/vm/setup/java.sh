#!/bin/bash

echo ">> Installing Java"

sudo DEBIAN_FRONTEND=noninteractive apt-get update -y
sudo DEBIAN_FRONTEND=noninteractive apt-get install default-jre -y
