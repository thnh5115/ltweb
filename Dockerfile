FROM php:8.0-apache

# Cài PHP dependencies + MariaDB server (MySQL compatible)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    mariadb-server \
    mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Tạo thư mục socket cho MariaDB và set quyền
RUN mkdir -p /run/mysqld && chown -R mysql:mysql /run/mysqld /var/lib/mysql

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Copy source code
WORKDIR /var/www/html
COPY . /var/www/html

# Copy script start và cho phép thực thi
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Phân quyền (nếu cần upload/logs)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Khi container start -> chạy script (start MariaDB + Apache)
CMD ["/usr/local/bin/start.sh"]
