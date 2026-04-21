
FROM php:8.3-fpm

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        wget \
        unzip \
        zip \
        libpq-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp

RUN docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        xml \
        bcmath \
        fileinfo \
        zip \
        gd \
        opcache \
        pcntl \
        sockets

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zzz-custom.conf

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

# Copiar entrypoint personalizado que ajusta permisos e instala dependencias si es necesario
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
