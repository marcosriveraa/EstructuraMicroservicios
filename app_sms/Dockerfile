FROM rabbitmq:3.8-management-alpine

RUN rabbitmq-plugins enable --offline rabbitmq_mqtt rabbitmq_federation_management rabbitmq_stomp 

# Usar una imagen base de PHP
FROM php:8.1-cli

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente de la aplicación PHP al contenedor
COPY ./src /var/www/html

# Exponer el puerto 80 para acceso al formulario si es necesario
EXPOSE 80

# Comando para mantener el contenedor en ejecución y escuchar solicitudes HTTP
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
