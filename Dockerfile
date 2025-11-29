# Image PHP + Apache (production)
FROM php:8.0-apache

# Cài dependencies cần cho GD + MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Bật mod_rewrite cho Apache (để .htaccess hoạt động)
RUN a2enmod rewrite

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ source code vào container
COPY . /var/www/html

# Set DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Phân quyền (chủ yếu cho upload / cache nếu bạn dùng)
RUN chown -R www-data:www-data /var/www/html
