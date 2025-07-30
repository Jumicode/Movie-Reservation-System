# Usa la imagen base de PHP-FPM con Alpine Linux para PHP 8.2
FROM php:8.2-fpm-alpine

# Instala herramientas del sistema y dependencias de desarrollo.
RUN apk update && apk upgrade && \ # Agregamos update y upgrade
    apk add --no-cache \
    build-base \
    nginx \
    supervisor \
    git \
    unzip \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    curl-dev \
    mariadb-client-dev \
    # Esto es solo si todavía fallaba la anterior. Si funcionaba, mantén la original.
    # Si quieres probar redis directamente via apk de nuevo, prueba php-redis en vez de php82-pecl-redis
    # php-redis \
    ;

# Habilita e instala extensiones de PHP.
# Se usa docker-php-ext-install para las extensiones nativas de PHP
# y pecl install para extensiones PECL como redis.
RUN docker-php-ext-install pdo_mysql \
    dom \
    xml \
    simplexml \
    session \
    json \
    mbstring \
    tokenizer \
    fileinfo \
    phar \
    opcache \
    curl \
    gd \
    zip \
    intl \
    mysqli && \
    # Instala la extensión Redis a través de PECL
    pecl install redis && \
    docker-php-ext-enable redis;

# Descarga e instala Composer de forma global en el contenedor.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todo el código de tu aplicación al contenedor
COPY . .

# Instala las dependencias de Composer (sin desarrollo)
RUN composer install --no-dev --optimize-autoloader

# Optimiza la configuración de Laravel
RUN php artisan config:cache \
    php artisan route:cache \
    php artisan view:cache

# Crea las carpetas necesarias para Nginx y PHP-FPM
RUN mkdir -p /etc/nginx/http.d/ /etc/php8/php-fpm.d/

# Copia la configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copia la configuración de PHP-FPM (opcional)
COPY docker/php-fpm/www.conf /etc/php8/php-fpm.d/www.conf

# Crea el enlace simbólico para el almacenamiento
RUN php artisan storage:link

# Expone el puerto 80 para Nginx
EXPOSE 80

# Define el comando de inicio para Nginx y PHP-FPM
CMD ["sh", "-c", "php-fpm82 && nginx -g 'daemon off;'"]