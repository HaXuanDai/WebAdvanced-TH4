# 1. Dùng base PHP image kèm Apache
FROM php:8.2-apache

# 2. Cài các extension Laravel cần
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git \
    && docker-php-ext-install pdo pdo_mysql zip

# 3. Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Tạo thư mục ứng dụng Laravel
WORKDIR /var/www/html

# 5. Copy toàn bộ mã nguồn
COPY . .

# 6. Cài thư viện Laravel bằng Composer
RUN composer install --no-dev --optimize-autoloader

# 7. Phân quyền cho Laravel (rất quan trọng)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# 8. Bật Rewrite Module cho Apache
RUN a2enmod rewrite

# 9. Cấu hình Apache để Laravel hoạt động đúng (dùng .htaccess)
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# 10. Expose port (Render cần)
EXPOSE 80

# 11. Laravel sẽ chạy qua Apache (nên không cần CMD thêm)
