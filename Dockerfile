# Dockerfile — FRS RAMSAM PHP Application
# Uses php:8.2-cli with PHP built-in server (no Apache = no MPM conflicts)

FROM php:8.2-cli

# Install mysqli and PDO MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html 2>/dev/null || true

# Railway sets $PORT dynamically at runtime
# PHP built-in server listens on 0.0.0.0:$PORT
EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /var/www/html router.php"]
