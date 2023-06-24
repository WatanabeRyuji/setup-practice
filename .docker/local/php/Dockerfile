FROM php:8.2-fpm-buster

COPY php.ini /usr/local/etc/php/php.ini

# ecrにイメージが保存されたら削除する -- ここから --
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    mbstring \
    zip \
    opcache \
    gd \
    intl \
    redis \
    decimal \
    xdebug

RUN curl -sl https://deb.nodesource.com/setup_lts.x | bash -
RUN apt install -y nodejs
RUN npm install --global yarn

# ecrにイメージが保存されたら削除する -- ここまで --

RUN apt install -y fish

COPY entrypoint.sh /opt/docker-entrypoint.sh

ENTRYPOINT ["/opt/docker-entrypoint.sh"]

WORKDIR /app

CMD "php-fpm"
