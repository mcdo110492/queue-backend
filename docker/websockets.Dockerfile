FROM php:7.2-alpine

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apk update && apk add --no-cache \
    mysql-client \
    zip \
    vim \
    unzip \
    curl \ 
    python


RUN curl "https://bootstrap.pypa.io/get-pip.py" -o "get-pip.py"
RUN python get-pip.py
RUN pip install supervisor

COPY ./docker/supervisor/websockets/supervisord.conf   /etc/supervisord.conf

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



CMD ["supervisord"]