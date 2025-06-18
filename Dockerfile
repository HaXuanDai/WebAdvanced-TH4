FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libonig-dev libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libicu-dev \
  && docker-php-ext-configure gd \
       --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
       pdo pdo_mysql zip mbstring tokenizer xml intl gd \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

RUN a2enmod rewrite
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
