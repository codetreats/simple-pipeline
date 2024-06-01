FROM codetreats/simple-pipeline:0.16.0-rc.3-SNAPSHOT

RUN mkdir /var/www/html/my_important_job_data
RUN echo "Job Data=>/my_important_job_data" >> /var/www/html/pipeline/menu.txt
COPY job.sh /job/
COPY before.sh /job/
COPY my_important_step.sh /job/my_important_step.sh

RUN echo "0 10 * * * root /trigger.sh" > /etc/cron.d/cronjob
CMD ["/start.sh"]


