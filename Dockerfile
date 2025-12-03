# Dockerfile
# Use the official PHP image with Apache pre-installed as the base
FROM php:7.4-apache

# Set the author/maintainer label
LABEL maintainer="Rclaye Clixx Development"

# Install necessary dependencies, including a database driver (mysqli)
# and utilities often needed by WordPress and similar apps
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    vim \
    mariadb-client && \
    docker-php-ext-install mysqli pdo pdo_mysql gd zip && \
    # Fix Apache ServerName warning
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy your application source code into the webroot directory
COPY . /var/www/html/

# Copy and set permissions for the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Update ownership of the web root to the Apache user
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80 (standard HTTP)
EXPOSE 80

# Use our custom entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]