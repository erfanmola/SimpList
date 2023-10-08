FROM openswoole/swoole:latest-alpine

WORKDIR /var/www

COPY . /var/www

RUN composer install --working-dir=/var/www

EXPOSE ${OPENSWOOLE_SERVER_PORT}

CMD [ "php", "server.php" ]