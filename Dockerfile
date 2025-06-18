FROM php:8.2-apache

# Cài thư viện và PHP extension
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    curl \
    git \
    zip \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    xml \
    intl \
    gd \

    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Bật module rewrite cho Laravel hoặc framework khác
RUN a2enmod rewrite

# Copy mã nguồn vào container
COPY . /var/www/html

# Đặt thư mục làm việc
WORKDIR /var/www/html
