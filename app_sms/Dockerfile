# Usar una imagen base de PHP
FROM php:8.1-cli

# Instalar dependencias necesarias (php-amqplib para RabbitMQ)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libssl-dev \
    libsasl2-dev \
    pkg-config \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente de la aplicación PHP al contenedor
COPY ./src /var/www/html

# Exponer el puerto 80 para acceso al formulario si es necesario
EXPOSE 80

# Comando para mantener el contenedor en ejecución y escuchar solicitudes HTTP
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
