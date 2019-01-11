FROM php:7.0-apache

RUN apt-get update
RUN apt-get install -y git unzip vim wget

RUN apt-get install libc-l10n locales
RUN echo "en_GB.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "es_ES.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen
RUN locale-gen

ADD . /var/www/html
COPY config.ttl.dist /var/www/html/
RUN mv config.ttl.dist config.ttl
COPY .htaccess .

RUN docker-php-ext-install gettext
RUN wget https://getcomposer.org/composer.phar

RUN php composer.phar install --no-dev
RUN a2enmod rewrite
