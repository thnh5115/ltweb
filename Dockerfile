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

# (Optional) Nếu cần chỉnh DocumentRoot, có thể dùng APACHE_DOCUMENT_ROOT:
# ENV APACHE_DOCUMENT_ROOT=/var/www/html
# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
#     /etc/apache2/sites-available/000-default.conf \
#     /etc/apache2/sites-available/default-ssl.conf

# Phân quyền (chủ yếu cho upload / cache nếu bạn dùng)
RUN chown -R www-data:www-data /var/www/html
