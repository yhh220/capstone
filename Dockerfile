# ---- Stage 1: PHP deps ----
# vendor/ has to exist before the frontend build, because admin.css imports
# Filament's theme.css straight out of vendor/filament/filament/resources/css.
# Same PHP version and extension set as the runtime stage below: composer.lock
# was generated against local PHP 8.5 (pins symfony/console >=8.4), and
# composer's platform check needs ext-intl/exif/gd present to resolve cleanly.
FROM php:8.4-cli AS vendor
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libonig-dev libicu-dev ca-certificates \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip bcmath intl exif \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ---- Stage 2: build frontend assets ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
# Not npm ci: the lockfile was generated with a newer local npm than the one
# bundled with Render's node:20 image, and npm's optional-dependency sync
# check for native binaries (lightningcss/oxide) disagreed across versions.
RUN npm install
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---- Stage 3: PHP application runtime ----
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libonig-dev libicu-dev ca-certificates \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip bcmath intl exif \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN chmod -R 775 storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["docker-entrypoint.sh"]
