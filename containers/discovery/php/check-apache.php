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
    $cmd_return = shell_exec("etcdctl --endpoints='$etcd_endpoints' get /services/graphite; echo $?");
    $cmd_return = array_pop(explode("\n", trim($cmd_return)));
    error_log('cmd_return = '.var_export($cmd_return, 1));
    if ('0' == $cmd_return) {
        error_log('/services/graphite is not empty');
        $grafana_url = 'http://'.trim(shell_exec("etcdctl --endpoints='$etcd_endpoints' get /services/graphite"));
        create_graphite_datasource_if_missing($grafana_url);
    }
    else
        error_log('/services/graphite is empty');

    // Sleep
    sleep(20);
}
