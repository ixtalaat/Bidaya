#!/bin/bash
# Railway pre-deploy: migrate, link storage, warm caches.
# Make executable: git update-index --chmod=+x railway/init-app.sh
set -e

php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
