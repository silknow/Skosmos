FROM php:7.2-apache

RUN apt-get update

RUN apt-get -y install locales
RUN echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "en_GB.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "fi_FI.UTF-8 UTF-8" >> /etc/locale.gen
RUN echo "sv_SE.UTF-8 UTF-8" >> /etc/locale.gen
RUN locale-gen

RUN yes | pecl install xdebug && docker-php-ext-enable xdebug \
      && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/php.ini  \
      && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.remote_host=docker.for.mac.localhost" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.idekey=IDEA_DEBUG" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.remote_log=/tmp/xdebug.log" >> /usr/local/etc/php/php.ini \
      && echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/php.ini

RUN a2enmod rewrite
RUN docker-php-ext-install gettext
