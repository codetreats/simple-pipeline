FROM ubuntu:24.04
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC apt-get -y install tzdata
RUN apt-get update && apt-get install -q -y \
    aptitude nano inotify-tools curl wget jq zip cron dos2unix rsync iputils-ping software-properties-common \
    apache2 php libapache2-mod-php php-cli php-common php-imap php-curl php-yaml \
    python3 python3-pip \
    openjdk-17-jdk
RUN curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg
RUN echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | tee /etc/apt/sources.list.d/github-cli.list > /dev/null
RUN apt-get update && apt-get install -q -y gh
RUN mkdir -p /pipeline /var/www/html/pipeline/trigger /var/www/html/pipeline/status /var/www/html/pipeline/logs
RUN true > /etc/cron.d/cronjob
COPY bin/* /bin/
RUN chmod +x /bin/*
COPY www/ /var/www/html/pipeline/
COPY www/redirect.html /var/www/html/index.html
COPY src/pipeline /pipeline
COPY src/menu.txt /var/www/html/pipeline/menu.txt
COPY src/trigger.sh /
COPY src/update.sh /
COPY src/mail.php /mail.php
RUN mkdir /job
COPY start.sh /
RUN chown -R www-data:www-data /var/www

CMD ["/start.sh"]