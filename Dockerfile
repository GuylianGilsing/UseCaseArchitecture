FROM php:8.2-cli-alpine3.18 as build

# Setup PHP project structure
RUN mkdir /app
RUN mkdir /app/example-api
RUN mkdir /app/framework

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

# Copy example API files
COPY ./example-api/public /app/example-api/public
COPY ./example-api/src /app/example-api/src
COPY ./example-api/tests /app/example-api/tests
COPY ./example-api/composer.json /app/example-api
COPY ./example-api/composer.lock /app/example-api
COPY ./example-api/phpinsights.php /app/example-api
COPY ./example-api/phpunit.xml /app/example-api

# Copy framework files
COPY ./framework/src /app/framework/src
COPY ./framework/tests /app/framework/tests
COPY ./framework/composer.json /app/framework
COPY ./framework/composer.lock /app/framework
COPY ./framework/phpinsights.php /app/framework
COPY ./framework/phpunit.xml /app/framework

# Install framework project
WORKDIR /app/framework

RUN composer install
RUN composer dump-autoload -o

RUN composer run lint
RUN composer run tests

# Install example API project
WORKDIR /app/example-api

RUN composer install
RUN composer dump-autoload -o

RUN composer run lint
RUN composer run tests

FROM php:8.2-cli-alpine3.18 as serve

# Setup PHP project structure
RUN mkdir /app
COPY --from=build /app/example-api /app/example-api
COPY --from=build /app/framework /app/framework

WORKDIR /app/example-api/public

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080"]
