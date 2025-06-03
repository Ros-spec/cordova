# Usa la imagen oficial de PHP con servidor embebido
FROM php:8.2-cli

# Copia todos los archivos de tu proyecto al contenedor
COPY . /app

# Establece el directorio de trabajo
WORKDIR /app

# Expone el puerto 10000 (puedes cambiarlo si quieres)
EXPOSE 10000

# Ejecuta el servidor PHP embebido en todas las interfaces
CMD ["php", "-S", "0.0.0.0:10000"]
