FROM php:8.2-cli

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update -o Acquire::Retries=5 \
    && apt-get install -y --no-install-recommends --fix-missing \
        curl \
        git \
        unzip \
        libreoffice \
        default-jre \
        ocrmypdf \
        tesseract-ocr \
        tesseract-ocr-eng \
        tesseract-ocr-rus \
        poppler-utils \
        python3 \
        python3-pip \
        libcurl4-openssl-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) mbstring xml gd zip curl fileinfo \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -L -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
    && chmod +x /usr/local/bin/composer

RUN mkdir -p /opt/tabula \
    && curl -L -o /opt/tabula/tabula.jar https://github.com/tabulapdf/tabula-java/releases/download/v1.0.5/tabula-1.0.5-jar-with-dependencies.jar

WORKDIR /var/www/backend

COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

COPY backend /var/www/backend
COPY worker /var/www/worker

RUN mkdir -p /var/www/backend/storage/uploads /var/www/backend/storage/outputs \
    && chown -R www-data:www-data /var/www/backend/storage

CMD ["php", "/var/www/worker/worker.php"]
