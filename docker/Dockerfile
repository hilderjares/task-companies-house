############################## BASE #####################################
FROM php:7.4-apache as base

RUN apt-get update && apt-get install -y \
    curl \
    g++ \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libpng-dev \
    libreadline-dev \
    sudo \
    unzip \
    zip \
 && docker-php-ext-install -j$(nproc) sockets pdo pdo_mysql mysqli && rm -rf /var/lib/apt/lists/*

##########################################################################
FROM base as dependencies

RUN useradd me

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY /composer.json /home/me/.composer/composer.json
RUN chown -R me:me /home/me/.composer

##########################################################################
FROM dependencies as cli

USER me

WORKDIR /var/www/app

############################ SERVER ######################################
FROM base

WORKDIR /var/www/app

RUN a2enmod rewrite

ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

COPY php.ini /etc/php/7.3/apache2/php.ini

ADD app.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND