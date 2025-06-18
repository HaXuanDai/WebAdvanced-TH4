FROM php:8.2-apache

# Cài các extension cần thiết
RUN docker-php-ext-install pdo pdo_mysql

# Bật mod_rewrite
RUN a2enmod rewrite

# Copy source code vào container
COPY . /var/www/html

# Laravel cần folder public là document root
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Thay đổi Apache config để trỏ đúng public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Cho phép .htaccess hoạt động
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

EXPOSE 80
