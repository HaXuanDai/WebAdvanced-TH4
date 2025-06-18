# PHP base với Apache
FROM php:8.2-apache

# Cài các thư viện bắt buộc
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libonig-dev libxml2-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql zip mbstring tokenizer xml gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Laravel app source
WORKDIR /var/www/html
COPY . .

# Cài Laravel dependencies (không include dev trong môi trường production)
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Bật mod_rewrite cho Laravel routing
RUN a2enmod rewrite

# Copy file cấu hình Apache nếu có
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
