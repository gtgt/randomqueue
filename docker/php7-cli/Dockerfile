FROM php:7.2-cli-alpine
ARG TIMEZONE

#RUN apt-get update && apt-get install -y git unzip openssl procps acl
RUN apk add --no-cache $PHPIZE_DEPS openssl-dev procps bash

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN mkdir /var/log/php
RUN chmod 777 /var/log/php
RUN printf '[PHP]\nlog_errors = On\nerror_log = /var/log/php/error.log\n' > /usr/local/etc/php/conf.d/error_log.ini

# Install extensions
RUN docker-php-ext-install pdo_mysql bcmath

# Create an alias for console
RUN echo 'alias console="php /randomqueue/bin/console"' >> ~/.bashrc

WORKDIR /randomqueue
