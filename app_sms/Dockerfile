# Usar la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev && \
    docker-php-ext-install sockets

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar solo los archivos de Composer primero (optimiza el uso de caché de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias antes de copiar el resto del proyecto
RUN composer install --no-interaction --no-dev --prefer-dist

# Copiar todos los archivos del proyecto
COPY . .

# Asegurar que `vendor` y `storage` sean accesibles
RUN chmod -R 777 /var/www/html/vendor

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# Ejecutar Apache
CMD ["apache2-foreground"]
