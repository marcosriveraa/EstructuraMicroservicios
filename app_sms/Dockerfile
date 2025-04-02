# Usar una imagen base de PHP
FROM php:8.1-cli

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libssl-dev \
    libsasl2-dev \
    pkg-config \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install sockets \
    && apt-get clean

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente de la aplicación PHP al contenedor
COPY ./src /var/www/html

# Exponer el puerto 80 para acceso al formulario si es necesario
EXPOSE 80

# Comando para mantener el contenedor en ejecución y escuchar solicitudes HTTP
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]

# Ejecutar Composer install cuando el contenedor se construya
RUN composer install
