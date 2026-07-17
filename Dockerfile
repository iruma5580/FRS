# Dockerfile — FRS RAMSAM PHP Application
# Base: php:8.2-apache (mysqli compiled in via docker-php-ext-install)

FROM php:8.2-apache

# Disable mpm_event, enable mpm_prefork (required for mod_php / mysqli)
RUN a2dismod mpm_event 2>/dev/null || true \
 && a2enmod mpm_prefork rewrite

# Compile and install PHP MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Allow .htaccess overrides
RUN { \
    echo '<Directory /var/www/html>'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/frs.conf \
 && a2enconf frs

# Set working directory and copy application files
WORKDIR /var/www/html
COPY . .

# Fix file permissions
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type f -exec chmod 644 {} \; \
 && find /var/www/html -type d -exec chmod 755 {} \;

# Copy and prepare the entrypoint script (avoid CRLF issues)
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r//' /usr/local/bin/entrypoint.sh \
 && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
