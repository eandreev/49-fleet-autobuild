<?php

require_once __DIR__.'/HttpRequest.php';

if(!isset($_SERVER['argv'][1]) || !isset($_SERVER['argv'][2])) {
    error_log('Usage: php '.basename($_SERVER['argv'][0]).' etcd-endpoints ip');
    exit(1);
}

$etcd_endpoints = $_SERVER['argv'][1];
$ip             = $_SERVER['argv'][2];

while(1) {
    $cmd_return = shell_exec("ncat -v --recv-only -i 1 $ip 7002 2>&1");
    if(false !== strpos($cmd_return, 'Connected to'))
        system("etcdctl --endpoints='$etcd_endpoints' set /services/graphite '$ip' --ttl 30;");
    else
        system("etcdctl --endpoints='$etcd_endpoints' rm /services/graphite || true");
    sleep(20);
}

