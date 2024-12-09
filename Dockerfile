FROM php:8.2-fpm

# Actualizar paquetes y agregar herramientas necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    curl \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar permisos para el directorio del proyecto
RUN usermod -u 1000 www-data && \
    chown -R www-data:www-data /var/www

# Definir directorio de trabajo
WORKDIR /var/www

# Copiar el código fuente
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a la carpeta storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
