#!/usr/bin/env bash

set -eux

apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
	icu-dev
docker-php-ext-install -j "$(nproc)" \
	intl \
	opcache \
	pdo_mysql

# Find packages to keep, so we can safely delete dev packages
RUN_DEPS="$(
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions |
        tr ',' '\n' |
        sort -u |
        awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }'
)"
# shellcheck disable=SC2086
apk add --virtual .phpexts-rundeps $RUN_DEPS

# Remove building tools for smaller container size
apk del .build-deps

# Install Symfony tool
apk add --no-cache git
wget https://get.symfony.com/cli/installer -O - | bash
mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Get all vendors
cd /build/src
if [[ "$DEV" == "true" ]]; then
    composer install --prefer-dist --no-interaction --no-plugins --no-scripts --no-suggest --optimize-autoloader
else
    composer install --prefer-dist --no-dev --no-interaction --no-plugins --no-scripts --no-suggest --optimize-autoloader
fi

# Copy the final app to /app
mkdir /app
mv ./bin /app
mv ./config /app
mv ./public /app
mv ./src /app
mv ./templates /app
mv ./translations /app
mv ./vendor /app

# Fix permission
cd /app
chown www-data:www-data -R .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Cleanup
rm -r /build
rm /usr/bin/composer
