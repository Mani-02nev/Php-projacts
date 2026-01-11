# Use official PHP with Apache image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite headers expires deflate

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache to process PHP files and allow .htaccess
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php index.html\n\
    <FilesMatch \.php$>\n\
    SetHandler application/x-httpd-php\n\
    </FilesMatch>\n\
    </Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf \
    && a2enconf docker-php

# Expose port 80
EXPOSE 80

# Set environment variable for port
ENV PORT=80

# Start Apache with PHP support
CMD ["apache2-foreground"]
