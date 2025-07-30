# Usa una imagen base de PHP-FPM con Alpine Linux (ligera y eficiente). 
# Puedes cambiar '8.3-fpm-alpine' por tu versión de PHP, por ejemplo '8.2-fpm-alpine'.
FROM php:8.3-fpm-alpine

# Instala dependencias del sistema y extensiones de PHP comunes para Laravel.
# 'apk add' es el gestor de paquetes de Alpine Linux.
RUN apk add --no-cache nginx \
    php8-mysqli \
    php8-pdo_mysql \
    php8-dom \
    php8-xml \
    php8-simplexml \
    php8-session \
    php8-json \
    php8-mbstring \
    php8-tokenizer \
    php8-fileinfo \
    php8-phar \
    php8-opcache \
    php8-curl \
    php8-gd \
    php8-zip \
    php8-intl \
    php8-pecl-redis \
    supervisor \
    git \
    unzip

# Descarga e instala Composer de forma global en el contenedor.
# Composer es la herramienta para gestionar las dependencias de PHP.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo dentro del contenedor.
# Aquí es donde se copiarán los archivos de tu proyecto.
WORKDIR /var/www/html

# Copia todo el código de tu aplicación al contenedor.
# El primer '.' es el origen (tu proyecto local), el segundo '.' es el destino (/var/www/html en el contenedor).
COPY . .

# Instala las dependencias de Composer (sin desarrollo) dentro del contenedor.
# '--no-dev' para no instalar dependencias de desarrollo.
# '--optimize-autoloader' para un mejor rendimiento en producción.
RUN composer install --no-dev --optimize-autoloader

# Optimiza la configuración de Laravel para producción.
# Estas cachés mejoran la velocidad de tu aplicación.
RUN php artisan config:cache \
    php artisan route:cache \
    php artisan view:cache

# Configuración de Nginx y PHP-FPM:
# Render necesita que le digas cómo configurar Nginx (el servidor web) y PHP-FPM (el procesador de PHP).
# Crearemos una carpeta 'docker' con estas configuraciones.

# Crea las carpetas necesarias para Nginx y PHP-FPM
RUN mkdir -p /etc/nginx/http.d/ /etc/php8/php-fpm.d/

# Copia la configuración de Nginx desde tu proyecto a la ubicación correcta en el contenedor.
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copia la configuración de PHP-FPM desde tu proyecto a la ubicación correcta en el contenedor.
# Esto es opcional, a menudo la configuración por defecto es suficiente.
COPY docker/php-fpm/www.conf /etc/php8/php-fpm.d/www.conf

# Crea el enlace simbólico para el almacenamiento (si usas 'php artisan storage:link').
# Esto es importante si tus cargas de archivos (imágenes de Filament, por ejemplo) se guardan en storage/app/public.
RUN php artisan storage:link

# Expone el puerto 80 para Nginx.
EXPOSE 80

# Define el comando de inicio para Nginx y PHP-FPM.
# Esto le dice a Docker cómo arrancar los servicios cuando el contenedor se inicie.
CMD ["sh", "-c", "php-fpm8 && nginx -g 'daemon off;'"]