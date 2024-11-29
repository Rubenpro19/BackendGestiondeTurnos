# Etapa 1: Construcción de dependencias
FROM php:8.1-fpm AS build

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos de Laravel
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Etapa 2: Configuración de producción
FROM nginx:1.21

# Copiar configuración de Nginx
COPY .docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copiar el código de Laravel desde la etapa de construcción
COPY --from=build /var/www/html /var/www/html

# Configurar permisos
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

CMD [ "/deploy.sh" ]