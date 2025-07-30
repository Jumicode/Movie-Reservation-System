# Usa la imagen base de PHP-FPM con Alpine Linux para PHP 8.2
FROM php:8.2-fpm-alpine

# Instala dependencias del sistema y extensiones de PHP comunes para Laravel.
RUN apk add --no-cache nginx \
    php82-mysqli \
    php82-pdo_mysql \
    php82-session \
    php82-json \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-phar \
    php82-opcache \
    php82-curl \
    php82-pecl-redis \
    supervisor \
    git \
    unzip \
    # Dependencias para 'dom', 'xml', 'simplexml'
    libxml2-dev \
    # Dependencias para 'gd'
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Dependencias para 'zip'
    libzip-dev \
    # Dependencias para 'intl'
    icu-dev

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
    mysqli

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