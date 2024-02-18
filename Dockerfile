FROM php:8.2.0-fpm

# Install required dependencies and PHP extensions
RUN apt-get update && apt-get install -y  \
        unzip \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]



