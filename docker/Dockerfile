FROM php:8.2-fpm

ARG PHP_FPM_OUT_PORT

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY src/composer.json ./composer.json

RUN composer install --no-dev --optimize-autoloader

COPY src/ /var/www/html

RUN groupadd -g 33 www-data && useradd -u 33 -g www-data -s /bin/sh www-data

RUN chown -R www-data:www-data /var/www/html

USER www-data

RUN echo "Exposing port ${PHP_FPM_OUT_PORT}"

EXPOSE ${PHP_FPM_OUT_PORT}

CMD ["php-fpm"]
