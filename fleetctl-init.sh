fleetctl submit `dirname $0`/unit-templates/*.service
fleetctl start \
    apache@{1..2} \
    apache-discovery@{1..2} \
    nginx.service \
    graphite.service \
    graphite-discovery.service

