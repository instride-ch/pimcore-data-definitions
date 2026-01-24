FROM ghcr.io/cors-gmbh/pimcore-docker/php-fpm-debug:8.4-alpine3.22-7.0-latest AS dev
RUN set -eux; \
    apk update; \
    apk add $PHPIZE_DEPS libxslt-dev  \
      libstdc++ \
      libx11 \
      libxrender \
      libxext \
      libssl3 \
      ca-certificates \
      fontconfig \
      freetype \
      ttf-dejavu \
      ttf-droid \
      ttf-freefont \
      ttf-liberation; \
    docker-php-ext-install xsl; \
    docker-php-ext-install sockets; \
    sync; \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo 'xdebug.idekey = PHPSTORM' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.mode = debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM dev AS behat
RUN apk update && \
    apk add chromium chromium-chromedriver

ENV PANTHER_NO_SANDBOX=1
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
ENV PIMCORE_SKIP_DB_SETUP=1
ENV PANTHER_NO_HEADLESS=0
ENV APP_ENV="test"
