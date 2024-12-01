FROM php:8.2-fpm

# Instalar extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Configurar cualquier otra dependencia necesaria
# Configurar PHP-FPM para escuchar en 0.0.0.0
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
# Exponer el puerto 10000
EXPOSE 10000