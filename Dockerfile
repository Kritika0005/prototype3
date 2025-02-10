# Use an official PHP runtime as the parent image
FROM php:7.4-apache

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Expose port 80 to the outside world
EXPOSE 80

# Run Apache server in the foreground
CMD ["apache2-foreground"]
