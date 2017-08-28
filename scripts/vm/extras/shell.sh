#!/bin/bash

echo ">> Configuring shell"

LINE='set completion-ignore-case on'
FILE='/etc/inputrc'
grep -qF "$LINE" "$FILE"  || echo "$LINE" | sudo tee --append "$FILE"
