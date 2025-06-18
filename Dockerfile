# Base image PHP + Apache
FROM php:8.2-apache

# Cài các extension cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring tokenizer xml gd \
    && apt-get clean

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy mã nguồn Laravel vào container
COPY . .

# Cài thư viện Laravel
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Bật mod_rewrite
RUN a2enmod rewrite

# Copy cấu hình Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80 (Render dùng)
EXPOSE 80
