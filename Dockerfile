FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    libicu-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

CMD ["php", "index.php"]
