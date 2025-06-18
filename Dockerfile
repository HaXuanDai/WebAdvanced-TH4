# PHP base với Apache
FROM php:8.2-apache

# Cài các gói cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libonig-dev libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo pdo_mysql zip mbstring tokenizer xml gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Cài composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Laravel source
WORKDIR /var/www/html
COPY . .

# Cài Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Phân quyền
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Bật mod_rewrite
RUN a2enmod rewrite

# Copy cấu hình apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
