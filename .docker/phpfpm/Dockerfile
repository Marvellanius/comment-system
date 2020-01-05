FROM php:7.4-fpm
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip
# Get Composer
RUN curl -sS https://getcomposer.org/installer | php \
                 && mv composer.phar /usr/local/bin/ \
                 && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer
# Install extensions
RUN docker-php-ext-install pdo pdo_mysql
