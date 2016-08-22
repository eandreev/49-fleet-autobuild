<?php

require __DIR__.'/HttpRequest.php';

if(!isset($_SERVER['argv'][1]) || !isset($_SERVER['argv'][2]) || !isset($_SERVER['argv'][3])) {
    error_log('Usage: php '.basename($_SERVER['argv'][0]).' ip port');
    exit(1);
}

$ip             = $_SERVER['argv'][1];
$port           = $_SERVER['argv'][2];
$etcd_endpoints = $_SERVER['argv'][3];

$url = "http://$ip:$port";

while(1) {
    $exception_caught = false;
    try {
        HttpRequest::perform('GET', $url);
    } catch(\Exception $e) {
        $exception_caught = true;
    } finally {
        if(!$exception_caught)
            system($q = "etcdctl --endpoints='$etcd_endpoints' set /services/apache/$ip '$ip:$port' --ttl 30;");
        else
            system("etcdctl --endpoints='$etcd_endpoints' rm /services/apache/$ip || true");
    }
    sleep(20);
}
