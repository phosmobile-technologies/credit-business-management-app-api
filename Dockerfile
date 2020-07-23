# The base image which has php7.2-fpm and nginx
FROM webdevops/php-nginx:7.3

# Set image metadata
LABEL version="1.0"
LABEL description="Application for the Nimasa dangerous goods graphql api with nginx and php-fpm"

# Set the web root from which nginx serves our app
ENV WEB_DOCUMENT_ROOT="/var/www/public"

# Install various packages, php extensions and composer
RUN apt-get update && apt-get install -y libmcrypt-dev default-mysql-client zip libzip-dev libpq-dev libpng-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_mysql zip pgsql pdo pdo_pgsql gd \
    && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts

WORKDIR /var/www

# Copy our source code into the container
COPY --chown=www-data:www-data . /var/www

# Install dependencies
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --prefer-dist && rm -rf /root/.composer


# Finish composer
RUN composer dump-autoload

RUN chmod -R 777 /var/www/storage/

EXPOSE 80
