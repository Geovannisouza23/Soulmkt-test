# Use the official PHP CLI image
FROM php:8.1-cli

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html

# Expose port 8101 for the PHP built-in server
EXPOSE 8101

# Run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8101", "-t", "/var/www/html"]