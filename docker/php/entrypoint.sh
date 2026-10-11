#!/bin/sh

set -e

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# Link relatif (bukan /var/www/html/...) supaya folder yang sama juga bisa dipakai
# `php artisan serve` native di Windows. Link yang sudah ada tidak diubah.
if [ ! -e public/storage ] && [ ! -L public/storage ]; then
    ln -s ../storage/app/public public/storage
fi

exec "$@"
