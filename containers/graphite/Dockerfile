FROM jlachowski/grafana-graphite-statsd:2.5.0

COPY defaults.ini /opt/grafana/conf/defaults.ini
RUN mkdir -p /var/lib/grafana/dashboards/
COPY dashboards/* /var/lib/grafana/dashboards/
