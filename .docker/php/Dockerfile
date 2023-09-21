ARG PHP_VER
FROM php:${PHP_VER}-fpm as base
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends \
        make libicu-dev libzip-dev unzip libgmp-dev tzdata \
    && docker-php-ext-install -j$(nproc) \
        zip intl pdo_mysql gmp bcmath


FROM base as development
ARG USER_ID
ARG GROUP_ID
RUN addgroup --system --gid ${GROUP_ID} app-group \
    && adduser --system --gid ${GROUP_ID} --uid ${USER_ID} app-user \
    && chown -R ${USER_ID}:${GROUP_ID} /app
USER ${USER_ID}:${GROUP_ID}
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY php/php.ini-development /usr/local/etc/php/php.ini


FROM base as build
RUN chown -R www-data: /app
USER www-data:www-data
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock /app/
RUN composer install --no-scripts --no-plugins --no-interaction --no-progress
COPY --chown=www-data . /app/
RUN composer run-script post-install-cmd


FROM base as production
RUN chown -R www-data: /app
USER www-data:www-data
COPY --chown=www-data --from=build /app/ /app/
RUN cp -u /app/.env.example /app/.env
COPY .docker/php/php.ini-production /usr/local/etc/php/php.ini
