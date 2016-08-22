<?php

require_once __DIR__.'/HttpRequest.php';
require_once __DIR__.'/grafana-utils.php';

if(!isset($_SERVER['argv'][1]) || !isset($_SERVER['argv'][2]) || !isset($_SERVER['argv'][3])) {
    error_log('Usage: php '.basename($_SERVER['argv'][0]).' etcd-endpoints ip port');
    exit(1);
}

$etcd_endpoints = $_SERVER['argv'][1];
$ip             = $_SERVER['argv'][2];
$port           = $_SERVER['argv'][3];

$url = "http://$ip:$port";

while(1) {
    // Register webserver's ip
    $exception_caught = false;
    try {
        HttpRequest::perform('GET', $url);
    } catch(\Exception $e) {
        $exception_caught = true;
    } finally {
        if(!$exception_caught) {
            system($q = "etcdctl --endpoints='$etcd_endpoints' set /services/apache/$ip '$ip:$port' --ttl 30;");
        } else
            system("etcdctl --endpoints='$etcd_endpoints' rm /services/apache/$ip || true");
    }

    // Register the webserver's dashboard on Grafana
    $cmd_return = shell_exec("etcdctl --endpoints='$etcd_endpoints' get /services/graphite &> /dev/null; echo $?");
    $cmd_return = trim($cmd_return);
    if ('0' === $cmd_return) {
        $grafana_url = 'http://'.trim(shell_exec("etcdctl --endpoints='$etcd_endpoints' get /services/graphite"));
        create_graphite_datasource_if_missing($grafana_url);
    }

    // Sleep
    sleep(20);
}
