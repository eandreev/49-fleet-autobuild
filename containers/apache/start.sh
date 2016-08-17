#!/bin/bash

sed -i '/Hostname/c Hostname '${COLLECTD_HOSTNAME} /etc/collectd/collectd.conf

supervisord -n