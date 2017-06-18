FROM vetal2409/nginx-php:latest

RUN echo "* * * * * php -v >> /var/log/cron/default.log 2>&1" >> /etc/cron.d/default

WORKDIR /var/www/norse
