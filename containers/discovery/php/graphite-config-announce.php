<?php

require __DIR__.'/HttpRequest.php';

if(!isset($_SERVER['argv'][1]) || !isset($_SERVER['argv'][2])) {
    error_log('Usage: php '.basename($_SERVER['argv'][0]).' etcd-endpoints ip');
    exit(1);
}

$etcd_endpoints = $_SERVER['argv'][1];
$ip             = $_SERVER['argv'][2];

while(1) {
    $cmd_return = '';
    $cmd_return = shell_exec("ncat -v --recv-only -i 1 $ip 7002 2>&1");
    if(false !== strpos($cmd_return, 'Connected to'))
        system($q = "etcdctl --endpoints='$etcd_endpoints' set /services/graphite/$ip '$ip' --ttl 30;");
    else
        system("etcdctl --endpoints='$etcd_endpoints' rm /services/graphite/$ip || true");
    sleep(20);
}










/*

  while true; do \
    ncat -v --recv-only -i 1 $COREOS_PRIVATE_IPV4 7002 2>&1 | grep "Connected to" > /dev/null; \
    if [ $? -eq 0 ]; then \
      etcdctl set /services/graphite/${COREOS_PRIVATE_IPV4} \'${COREOS_PRIVATE_IPV4}\' --ttl 30; \
    else \
      etcdctl rm /services/graphite/${COREOS_PRIVATE_IPV4} || true; \
    fi; \
    sleep 20; \
  done

 */
