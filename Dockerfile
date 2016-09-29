FROM php:5.6.26-alpine

ADD src /srv/
WORKDIR /srv
EXPOSE 9090
CMD "php -e -S 127.0.0.1:9090"