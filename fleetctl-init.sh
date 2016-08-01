fleetctl submit `dirname $0`/unit-templates/*.service
fleetctl start apache@1 apache@2 apache-discovery@1 apache-discovery@2 nginx.service

