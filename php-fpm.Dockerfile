FROM php:8.1-fpm

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# We'll use /var/www/html as the document root for the code
WORKDIR /var/www/html
