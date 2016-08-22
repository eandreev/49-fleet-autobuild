FROM ubuntu:14.04

RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install php5-cli php5-curl php5-memcached curl

RUN mkdir -p /var/www/php
ADD php/* /var/www/php/

RUN curl -L https://github.com/coreos/etcd/releases/download/v3.0.6/etcd-v3.0.6-linux-amd64.tar.gz -o etcd-v3.0.6-linux-amd64.tar.gz
RUN tar xzvf etcd-v3.0.6-linux-amd64.tar.gz
RUN mv etcd-v3.0.6-linux-amd64/etcdctl /usr/local/bin
RUN rm -r etcd-v3.0.6-linux-amd64/ etcd-v3.0.6-linux-amd64.tar.gz

CMD ['/bin/bash']
