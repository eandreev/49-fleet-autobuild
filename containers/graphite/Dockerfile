FROM ubuntu:14.04

RUN sudo apt-get update

RUN sudo apt-get install graphite-web graphite-carbon

COPY local_settings.py /etc/graphite/local_settings.py
COPY graphite-carbon /etc/default/graphite-carbon
COPY carbon.conf /etc/carbon/carbon.conf
COPY storage-schemas.conf /etc/carbon/storage-schemas.conf

# service carbon-cache start

RUN apt-get install apache2 libapache2-mod-wsgi
COPY apache2-graphite.conf /etc/apache2/sites-available/apache2-graphite.conf
RUN a2dissite 000-default
RUN a2ensite apache2-graphite

# service apache2 reload

# echo "test.count 4 `date +%s`" | nc -q0 127.0.0.1 2003

RUN apt-get install supervisor



EXPOSE 80