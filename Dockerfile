FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libssl-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring exif pcntl bcmath zip xml

# Instalar extensiones necesarias
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar Swoole para optimización
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Instalar XDebug para debugging
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instalar Node.js y npm para compilación de assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias PHP y Node
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && npm ci --only=production \
    && npm run build 2>/dev/null || true

# Configurar permisos
RUN chown -R www-data:www-data \
    storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configurar PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

CMD ["php-fpm"]
