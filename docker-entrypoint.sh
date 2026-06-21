#!/bin/sh
set -e

# Render's ephemeral filesystem starts fresh on every deploy, so re-link and
# re-cache on every boot rather than assuming anything survived from before.
php artisan storage:link --force
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
