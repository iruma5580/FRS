# Dockerfile for FRS — RAMSAM PHP Application
# php:8.2-apache with mysqli compiled in

FROM php:8.2-apache

# Fix Apache MPM conflict: disable event/worker, use prefork (required for mod_php)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork

# Enable required PHP extensions (compiled into PHP binary)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite for PHP routing
RUN a2enmod rewrite

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/frs.conf \
    && a2enconf frs

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Set correct file ownership
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Startup script: substitute Railway's $PORT at runtime, then start Apache
RUN printf '#!/bin/sh\nPORT="${PORT:-80}"\nsed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf\nsed -i "s/*:80/*:$PORT/" /etc/apache2/sites-enabled/000-default.conf\nexec apache2-foreground\n' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]

