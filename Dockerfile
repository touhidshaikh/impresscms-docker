FROM php:7.4-apache

LABEL maintainer="touhidshaikh22@gmail.com"
LABEL description="ImpressCMS - Docker Service"
LABEL version="1.0"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -y && apt-get install -y \
    libmcrypt-dev \
    g++ \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libmcrypt4 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
    curl \
    git \
    supervisor \
    net-tools 

RUN docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

RUN a2enmod rewrite

COPY ./impresscms /var/www/html/

WORKDIR /var/www/html/

RUN chmod -R 755 /var/www/html/

#Writeable
RUN chmod 777 mainfile.php
RUN chmod 777 uploads
RUN chmod 777 modules
RUN chmod 777 cache
RUN chmod 777 templates_c

#TrustPath
RUN mkdir /var/www/html/trustpath123
RUN chmod 777 /var/www/html/trustpath123

RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80

COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
