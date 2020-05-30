#!/usr/bin/env bash

set -eu

# If the user does not pass php-fpm, call whatever he wants to use (e. g. /bin/bash)
if [[ $1 != "php-fpm" ]]; then
    exec "$@"
    exit
fi

# Setup php
if [[ "$APP_ENV" == "dev" ]]; then
    cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
else
    cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
fi

{
    echo "opcache.revalidate_freq=0"
    if [[ "$APP_ENV" != "dev" ]]; then
        echo "opcache.validate_timestamps = 0"
    fi
    echo "opcache.max_accelerated_files = $(find /app -type f -print | grep -c php)"
    echo "opcache.memory_consumption = 192"
    echo "opcache.interned_strings_buffer = 16"
    echo "opcache.fast_shutdown = 1"
} >"$PHP_INI_DIR/conf.d/opcache.ini"
{
    echo "max_execution_time = 120"
    echo "memory_limit = 256M"
} >"$PHP_INI_DIR/conf.d/misc.ini"

# Add custom upload limit
if [[ ! -z "${UPLOAD_LIMIT}" ]]; then
    echo "Adding the custom upload limit."
    {
        echo "upload_max_filesize = $UPLOAD_LIMIT"
        # TODO: "post_max_size" should be greater than "upload_max_filesize".
        echo "post_max_size = $UPLOAD_LIMIT"
    } >"$PHP_INI_DIR/conf.d/upload-limit.ini"
fi

exec "$@"
