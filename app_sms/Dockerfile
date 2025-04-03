FROM php:8.1-cli

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install sockets

WORKDIR /var/www/html
COPY . .

RUN composer install

RUN a2enmod rewrite 

EXPOSE 80

CMD ["apache2-foreground"]