FROM php:8.5-fpm

ARG HOST_UID=1000
ARG HOST_GID=1000

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions pdo_pgsql amqp intl opcache zip xdebug

COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

# Run as the host user so vendor/ and var/ written through the bind mount stay host-owned.
RUN groupadd --gid ${HOST_GID} app \
    && useradd --uid ${HOST_UID} --gid ${HOST_GID} --create-home --shell /bin/bash app \
    && mkdir -p /home/app/.composer && chown -R app:app /home/app

ENV COMPOSER_HOME=/home/app/.composer

USER app
WORKDIR /var/www/app
