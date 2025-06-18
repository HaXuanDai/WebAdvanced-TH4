# 1. Base image
FROM php:8.2-apache

# 2. Cài các extension Laravel cần
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git \
    && docker-php-ext-install pdo pdo_mysql zip

# 3. Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Làm việc trong thư mục Laravel
WORKDIR /var/www/html

# 5. Copy trước composer để tận dụng cache
COPY composer.json composer.lock ./

# 6. Cài thư viện
RUN composer install --no-dev --optimize-autoloader

# 7. Copy toàn bộ project
COPY . .

# 8. Phân quyền
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Bật rewrite
RUN a2enmod rewrite

# 10. Expose port
EXPOSE 80
