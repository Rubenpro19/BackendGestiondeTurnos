# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Cambiar el directorio de trabajo
WORKDIR /var/www/html
