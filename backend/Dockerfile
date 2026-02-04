FROM php:8.2-fpm

ENV DEBIAN_FRONTEND=noninteractive
ARG APT_DEBUG="-o Debug::Acquire::http=true -o Debug::Acquire::https=true"

RUN set -eux; \
    apt-get update -o Acquire::Retries=5 ${APT_DEBUG}; \
    apt-get install -y --no-install-recommends --fix-missing ${APT_DEBUG} \
        curl \
        git \
        unzip \
        python3 \
        python3-pip \
        libcurl4-openssl-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" mbstring xml gd zip curl fileinfo; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    apt-get update -o Acquire::Retries=5 ${APT_DEBUG}; \
    apt-get install -y --no-install-recommends --fix-missing ${APT_DEBUG} \
        libreoffice \
        default-jre \
        ocrmypdf \
        tesseract-ocr \
        tesseract-ocr-eng \
        tesseract-ocr-rus \
        poppler-utils; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

RUN curl -L -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
    && chmod +x /usr/local/bin/composer

RUN mkdir -p /opt/tabula \
    && curl -L -o /opt/tabula/tabula.jar https://github.com/tabulapdf/tabula-java/releases/download/v1.0.5/tabula-1.0.5-jar-with-dependencies.jar

WORKDIR /var/www/backend

COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

COPY . /var/www/backend

RUN mkdir -p storage/uploads storage/outputs \
    && chown -R www-data:www-data storage

EXPOSE 9000
