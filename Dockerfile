# Usamos una imagen base de PHP 8.2 FPM Alpine
FROM php:8.2-fpm-alpine

# Establecemos el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Instalamos las dependencias del sistema necesarias
# Añadimos las extensiones necesarias para Filament
RUN apk add --no-cache \
    nginx \
    oniguruma-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    git \
    curl \
    bash \
    build-base \
    libpq-dev \
    jpeg-dev \
    icu-dev

# Instalamos las extensiones de PHP requeridas para tu aplicación, incluyendo intl
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Copiamos los archivos de la aplicación al contenedor.
COPY . .

# Copiamos el binario de Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Asignamos los permisos correctos al directorio de la aplicación antes de la instalación de dependencias.
# Esto previene el error de "dubious ownership".
RUN chown -R www-data:www-data /var/www/html

# Cambiamos al usuario www-data para las siguientes operaciones
USER www-data

# Instalamos las dependencias de la aplicación
RUN composer install --optimize-autoloader

# Volvemos al usuario root para los siguientes comandos
USER root

# Exponemos el puerto 9000 para que Nginx se pueda comunicar con PHP-FPM
EXPOSE 9000

# El comando de inicio para el contenedor, que ejecuta PHP-FPM
CMD ["php-fpm"]