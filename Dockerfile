FROM php:7.4-apache

LABEL maintainer="touhidshaikh22@gmail.com"
LABEL description="ImpressCMS - Docker Service"
LABEL version="1.0"

ENV DEBIAN_FRONTEND noninteractive

RUN apt update

RUN echo 'mariadb-server-10.0 mysql-server/root_password password password123' | debconf-set-selections
RUN echo 'mariadb-server-10.0 mysql-server/root_password_again password password123' | debconf-set-selections
RUN apt install -y mariadb-server

RUN requirements="libmcrypt-dev g++ libicu-dev libzip-dev zlib1g-dev libmcrypt4" \
    && apt-get update && apt-get install -y $requirements \
    && requirementsToRemove="libmcrypt-dev g++ libicu-dev" \
    && apt-get purge --auto-remove -y $requirementsToRemove \
    && apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

RUN apt-get update
RUN apt-get install -y curl git supervisor net-tools

RUN a2enmod rewrite

COPY ./impresscms /var/www/html/

RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80

COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
