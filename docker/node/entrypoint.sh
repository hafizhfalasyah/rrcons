#!/bin/sh

set -e

mkdir -p /var/www/html/node_modules /home/node/.npm

if [ "$(id -u)" = "0" ]; then
    chown -R node:node /var/www/html/node_modules /home/node/.npm
    exec su-exec node "$@"
fi

exec "$@"
