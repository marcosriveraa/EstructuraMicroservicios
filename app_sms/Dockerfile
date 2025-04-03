# Usar la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar Composer y extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev && \
    docker-php-ext-install sockets

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Asegurar que los permisos sean correctos
RUN chmod -R 777 /var/www/html

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# Ejecutar bash y dentro ejecutar composer install
CMD ["/bin/bash", "-c", "composer install --no-interaction --no-dev --prefer-dist && apache2-foreground"]
