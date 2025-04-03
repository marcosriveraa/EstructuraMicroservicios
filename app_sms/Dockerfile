# Usar la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias (curl, unzip y librerías para extensiones PHP)
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev && \
    docker-php-ext-install sockets

# Instalar Composer (gestor de dependencias para PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar los archivos del proyecto al contenedor
WORKDIR /var/www/html
COPY . .

# Instalar las dependencias de PHP definidas en composer.json
RUN composer install --no-interaction --no-dev --prefer-dist

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# Ejecutar Apache en primer plano
CMD ["apache2-foreground"]
