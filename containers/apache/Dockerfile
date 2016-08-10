FROM ubuntu:14.04

# install apache
RUN apt-get update
RUN apt-get install apache2 collectd collectd-utils supervisor -y
#python-setuptools -y

# Install supervisord
#RUN easy_install supervisor

COPY supervisord.conf /etc/supervisord.conf
COPY collectd.conf /etc/collectd/collectd.conf
COPY foreground.sh /etc/apache2/foreground.sh
COPY start.sh /start.sh

RUN chmod 755 /start.sh
RUN chmod 755 /etc/apache2/foreground.sh
#RUN mkdir /var/log/supervisor/

# modify index.html
RUN echo "<h1>Running from Docker on CoreOS "`date '+%Y-%m-%d %H:%M:%S'`"</h1>" > /var/www/html/index.html

# create the build date file
RUN date > /built-at.txt

EXPOSE 80

#CMD ["/bin/bash"]
CMD ["/bin/bash", "/start.sh"]
