# Usa la imagen oficial de PHP con servidor embebido
FROM php:8.2-cli
RUN docker-php-ext-install pdo pdo_mysql

COPY . /app
WORKDIR /app
EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000"]
