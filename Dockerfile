# Dockerfile
FROM php:7.4-apache

# Use the official PHP image with Apache pre-installed as the base

# Set the author/maintainer label
LABEL maintainer="CliXX Development Team"

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
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy your application source code into the webroot directory
# The application files (index.php, wp-config.php, etc.) are in the 
# same directory as this Dockerfile, which is the './html' context from Jenkins.
# The webroot for the Apache image is /var/www/html/
COPY . /var/www/html/

# Update ownership of the web root to the Apache user
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80 (standard HTTP)
EXPOSE 80

# The default command for the base image runs Apache, so we do not need a CMD instruction.