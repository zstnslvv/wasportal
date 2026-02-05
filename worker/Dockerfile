FROM php:8.2-cli

ENV DEBIAN_FRONTEND=noninteractive
ARG APT_DEBUG="-o Debug::Acquire::http=true -o Debug::Acquire::https=true"
ARG ALLOW_INSECURE_DOWNLOADS="false"
ENV CURL_OPTS="--retry 5 --retry-connrefused --retry-delay 2 --fail -k"
ENV ALLOW_INSECURE_DOWNLOADS=${ALLOW_INSECURE_DOWNLOADS}

RUN set -eux; \
    apt-get update -o Acquire::Retries=5 ${APT_DEBUG}; \
    apt-get install -y --no-install-recommends --fix-missing ${APT_DEBUG} \
        ca-certificates \
        curl \
        git \
        unzip \
        python3 \
        python3-pip \
        libonig-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
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

RUN set -eux; \
    curl ${CURL_OPTS} -L -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
    && chmod +x /usr/local/bin/composer

RUN set -eux; \
    mkdir -p /opt/tabula; \
    curl ${CURL_OPTS} -L -o /opt/tabula/tabula.jar https://github.com/tabulapdf/tabula-java/releases/download/v1.0.5/tabula-1.0.5-jar-with-dependencies.jar

WORKDIR /var/www/backend

COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

COPY backend /var/www/backend
COPY worker /var/www/worker

RUN mkdir -p /var/www/backend/storage/uploads /var/www/backend/storage/outputs \
    && chown -R www-data:www-data /var/www/backend/storage

CMD ["php", "/var/www/worker/worker.php"]
