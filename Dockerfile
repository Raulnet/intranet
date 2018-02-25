FROM php:5.6-apache

# install
RUN apt-get update && apt-get install -y \
    ghostscript \
    git \
    zlib1g-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libicu-dev \
    libfontconfig1 \
    libxrender1 \
    libxml2 \
    libxml2-dev \
    wget gnupg \
  && docker-php-source extract \
  && docker-php-ext-install mysqli pdo_mysql zip gd \
  && docker-php-source delete

RUN sed -i 's!/var/www/html!/var/www/html/web!g' /etc/apache2/apache2.conf
RUN sed -i 's!/var/www/html!/var/www/html/web!g' /etc/apache2/sites-available/000-default.conf

# Enable mod rewrite
RUN a2enmod actions rewrite headers expires

#PHP config
ADD docker/php.ini /usr/local/etc/php

# Composer
ADD composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer global require "hirak/prestissimo:^0.3" \
  && composer install --no-autoloader --no-scripts

# Add code
ADD . /var/www/html

# Xdebug - uncomment and rebuild image if you want it !
#RUN yes | pecl install xdebug \
#    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

# Dump autolad
RUN composer install --optimize-autoloader
RUN composer dump-autoload -o -a
RUN chown -R www-data /var/www/html/var/cache