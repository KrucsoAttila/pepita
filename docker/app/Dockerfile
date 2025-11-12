ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip rsync dos2unix \
    libpq-dev libzip-dev libicu-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libssl-dev curl \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql bcmath intl xml zip gd opcache \
 && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
