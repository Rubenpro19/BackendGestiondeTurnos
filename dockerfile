FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql

<<<<<<< HEAD
# Configurar cualquier otra dependencia necesaria
=======
# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Configuración del directorio de trabajo
WORKDIR /var/www

# Copiar todos los archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data /var/www

# Exponer el puerto 8080
EXPOSE 8080

# Iniciar PHP-FPM
CMD ["php-fpm"]
>>>>>>> 0ba507948c4b097f00b5b4bc49859890878b85f6
