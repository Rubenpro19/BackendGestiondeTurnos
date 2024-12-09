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

# Exponer el puerto para comunicación interna
EXPOSE 9000