FROM php:8.2-apache

# Enable Apache rewrite (optional)
RUN a2enmod rewrite

# Copy PHP files
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html
