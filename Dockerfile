FROM debian:trixie-slim

RUN apt-get update && apt-get install -y \
        curl wget ca-certificates vim nano lsb-release \
        zip unzip 7zip apt-transport-https gnupg \
    && curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ trixie main" > /etc/apt/sources.list.d/php.list \
    && apt-get update \
    && apt-get install -y \
        php8.5-cli php8.5-fpm \
        php8.5-pgsql php8.5-redis php8.5-zip php8.5-curl \
        php8.5-mbstring php8.5-xml php8.5-intl php8.5-bcmath \
        php8.5-gd php8.5-readline \
        nginx supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/log/php && chown www-data:www-data /var/log/php

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

USER www-data

RUN composer install --optimize-autoloader --no-interaction --prefer-dist

RUN npm ci
RUN npm run build

RUN php artisan storage:link

USER root

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/sites-available/default /etc/nginx/sites-available/default

COPY docker/php/php-fpm.conf /etc/php/8.5/fpm/php-fpm.conf
COPY docker/php/php.ini /etc/php/8.5/fpm/php.ini
COPY docker/php/pool.d/www.conf /etc/php/8.5/fpm/pool.d/www.conf

COPY docker/supervisor/web.conf /etc/supervisor/conf.d/web.conf

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["web"]
