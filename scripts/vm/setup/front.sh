#!/bin/bash

echo ">> Deleting existing node_modules directory"

rm -rf node_modules

echo ">> Installing NPM packages"
yarn

echo ">> Building front-end assets"
yarn run build
