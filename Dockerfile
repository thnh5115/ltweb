FROM php:8.0-apache

# Cài dependency cho PHP + MySQL server
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    default-mysql-server \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Copy code vào container
WORKDIR /var/www/html
COPY . /var/www/html

# Copy script start và cho phép thực thi
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Phân quyền (nếu cần upload/logs)
RUN chown -R www-data:www-data /var/www/html

# Port web
EXPOSE 80

# Khi container start sẽ chạy script start.sh (MySQL + Apache)
CMD ["/usr/local/bin/start.sh"]
