FROM debian:trixie
RUN apt-get update && apt-get install -q -y apache2 php libapache2-mod-php php-cli php-common nano inotify-tools
RUN mkdir -p /pipeline/src /var/www/html/trigger /var/www/html/status /var/www/html/logs
COPY www/ /var/www/html/
COPY src/ /pipeline/src
COPY job/ /job
COPY start.sh /
RUN chown -R www-data:www-data /var/www

CMD ["/start.sh"]
