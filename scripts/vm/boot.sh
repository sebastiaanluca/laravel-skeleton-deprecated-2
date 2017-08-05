#!/bin/bash

# Reload Supervisord apps on each boot (required because they're symlinked)
sudo service supervisor stop
sudo service supervisor start
