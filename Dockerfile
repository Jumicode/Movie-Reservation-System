# Usamos una imagen base de PHP 8.2 FPM Alpine
FROM php:8.2-fpm-alpine

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Instalamos las dependencias del sistema, incluyendo Caddy y Node.js/NPM
RUN apk add --no-cache \
    caddy \
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
    icu-dev \
    nodejs \
    npm

# Instalamos las extensiones de PHP, incluyendo intl
RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Copiamos todos los archivos de la aplicación al contenedor
COPY . .

# Copiamos el binario de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Asignamos los permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Cambiamos al usuario www-data para instalar dependencias de Composer y NPM
USER www-data

# Instalamos las dependencias de la aplicación
RUN composer install --no-dev --optimize-autoloader

# Instalamos y compilamos los activos de frontend
RUN npm install
RUN npm run build

# Publicamos los activos de Filament
RUN php artisan filament:assets 

# Volvemos al usuario root
USER root

# Copiamos el Caddyfile al contenedor
COPY Caddyfile /etc/caddy/Caddyfile

# Expone el puerto por defecto de Caddy, que es 80
EXPOSE 80

# Comando para iniciar Caddy y PHP-FPM
CMD ["/bin/bash", "-c", "caddy run --config /etc/caddy/Caddyfile & php-fpm"]