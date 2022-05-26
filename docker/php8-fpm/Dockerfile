FROM php:8.1-fpm

RUN apt-get update && apt-get install -y sudo nano

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN  apt-get update && \
    apt-get install -y libpq-dev \
  libxml2-dev \
  libzip-dev \
  libpng-dev \
  libjpeg-dev \
  zip \
  && docker-php-ext-configure zip \
  && docker-php-ext-install \
    zip \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    opcache

RUN apt-get install -y autoconf g++ make git \
  && docker-php-source extract \
  && pecl install \
    redis \
  && docker-php-ext-enable \
    redis \
  && docker-php-source delete \
  && mkdir /xdebug_installer && cd /xdebug_installer && git clone https://github.com/xdebug/xdebug.git && cd xdebug && sh ./rebuild.sh && cd / && rm -rf /xdebug_installer \
  && rm -rf /tmp/*

COPY conf.d/* /usr/local/etc/php/conf.d/

RUN adduser www-data sudo && usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

USER www-data

WORKDIR /app