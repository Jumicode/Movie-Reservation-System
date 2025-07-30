# Usa la imagen base de PHP-FPM con Alpine Linux para PHP 8.2
FROM php:8.2-fpm-alpine

# Instala dependencias del sistema y extensiones de PHP comunes para Laravel.
# Primero instalamos los paquetes con apk add, incluyendo libxml2-dev
RUN apk add --no-cache nginx \
    php82-mysqli \
    php82-pdo_mysql \
    php82-dom \
    php82-xml \
    php82-simplexml \
    php82-session \
    php82-json \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-phar \
    php82-opcache \
    php82-curl \
    php82-gd \
    php82-zip \
    php82-intl \
    php82-pecl-redis \
    supervisor \
    git \
    unzip \
    libxml2-dev \ # <--- ¡Añadido aquí!
    # También es una buena práctica instalar las dependencias de GD como libpng-dev, libjpeg-turbo-dev, etc.
    # aunque a veces php-gd ya las trae como dependencias transitivas.
    # Si gd falla más adelante, podríamos necesitar añadir:
    # libpng-dev \
    # libjpeg-turbo-dev \
    # freetype-dev \
    ;

# AHORA, habilita las extensiones usando docker-php-ext-install
# Esto es crucial para que PHP las cargue
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
    mysqli \
    ;

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