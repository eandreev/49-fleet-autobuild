#!/bin/bash

sed -i '/Hostname/c Hostname '${COREOS_PRIVATE_IPV4} /etc/collectd/collectd.conf

supervisord -n