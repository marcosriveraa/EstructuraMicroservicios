FROM php:8.1-apache

# Instalar Composer y extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev && \
    chmod -R 777 /var/www/html && \
    docker-php-ext-install sockets
    

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos del proyecto
WORKDIR /var/www/html
COPY . .

# Instalar dependencias PHP con Composer
RUN composer install --no-interaction --no-dev --prefer-dist

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
