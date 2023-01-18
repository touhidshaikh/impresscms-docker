FROM php:7.4-apache

LABEL maintainer="touhidshaikh22@gmail.com"
LABEL description="ImpressCMS - Docker Service"
LABEL version="1.0"

ENV DEBIAN_FRONTEND noninteractive
ENV MYSQL_ROOT_PASSWORD password123

RUN apt-get update -y && apt-get install -y \
    mariadb-server \
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

RUN service mysql start && mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$MYSQL_ROOT_PASSWORD'; FLUSH PRIVILEGES;"

RUN docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

RUN a2enmod rewrite

COPY ./impresscms /var/www/html/

WORKDIR /var/www/html/

RUN chmod -R 755 /var/www/html/impresscms
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80

COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
