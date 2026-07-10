#!/bin/sh
set -e

# Render's ephemeral filesystem starts fresh on every deploy, so re-link and
# re-cache on every boot rather than assuming anything survived from before.
php artisan storage:link --force
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# The built-in server is single-threaded by default: the page HTML and every
# asset request queue behind one another (and behind other visitors). Four
# workers let them load in parallel while staying inside the 512MB instance.
export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
