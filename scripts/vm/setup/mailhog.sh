#!/bin/bash

echo ">> Configuring Mailhog"

sudo systemctl daemon-reload
sudo systemctl enable mailhog.service
sudo systemctl start mailhog.service
