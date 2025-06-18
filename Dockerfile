# 1. Base image: PHP + Apache
FROM php:8.2-apache

# 2. Cài các extension Laravel cần
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring tokenizer xml

# 3. Cài Composer (phiên bản từ Docker chính thức)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Đặt thư mục làm việc
WORKDIR /var/www/html

# 5. Copy source code vào container
COPY . .

# 6. Bỏ giới hạn bộ nhớ khi cài bằng composer
ENV COMPOSER_MEMORY_LIMIT=-1

# 7. Cài đặt Laravel dependencies (tối ưu khi production)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# 8. Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 9. Bật mod_rewrite để Laravel hoạt động
RUN a2enmod rewrite

# 10. Copy file cấu hình Apache (nếu có)
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# 11. Laravel sẽ chạy qua Apache
EXPOSE 80
