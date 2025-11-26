FROM php:8.0-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html

# Adjust permissions
RUN chown -R www-data:www-data /var/www/html
