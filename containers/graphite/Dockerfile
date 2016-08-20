FROM jlachowski/grafana-graphite-statsd:2.5.0

COPY defaults.ini /opt/grafana/conf/defaults.ini
RUN mkdir -p /var/lib/grafana/dashboards/
ADD dashboards/* /var/lib/grafana/dashboards/

# Add the Grafite datasource
COPY sup.conf /tmp/sup.conf
COPY create-datasource.sh /tmp/create-datasource.sh
RUN chmod +x /tmp/create-datasource.sh
RUN cat /tmp/sup.conf
RUN supervisord -c /tmp/sup.conf; sleep 10; /tmp/create-datasource.sh
RUN ps auxww
RUN ls -lh /tmp/
RUN cat /tmp/grafana-webapp.log
RUN cat /opt/grafana/data/log/grafana.log
#RUN /bin/bash -c /tmp/create-datasource.sh
RUN ps auxw | egrep 'supervisord|grafana-server' | grep -v grep | awk '{ print $2 }' | \
    while read p; do kill -9 $p; done

