FROM php:8.1-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y unzip git curl libssl-dev libcurl4-openssl-dev \
    && docker-php-ext-install sockets

# Instalar Composer dentro del contenedor
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar los archivos del proyecto dentro del contenedor
COPY app_sms /var/www/html

# Instalar la librería php-amqplib automáticamente dentro del contenedor
RUN composer require php-amqplib/php-amqplib

# Comando por defecto al iniciar el contenedor
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]
