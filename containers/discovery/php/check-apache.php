<?php

require_once __DIR__.'/HttpRequest.php';
require_once __DIR__.'/grafana-utils.php';

if(
    !isset($_SERVER['argv'][1]) ||
    !isset($_SERVER['argv'][2]) ||
    !isset($_SERVER['argv'][3]) ||
    !isset($_SERVER['argv'][4])
) {
    error_log('Usage: php '.basename($_SERVER['argv'][0]).' machine-name etcd-endpoints ip port');
    exit(1);
}

$machine_name   = $_SERVER['argv'][1];
$etcd_endpoints = $_SERVER['argv'][2];
$ip             = $_SERVER['argv'][3];
$port           = $_SERVER['argv'][4];

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
    if ('0' == $cmd_return) {
        $grafana_url = 'http://'.trim(shell_exec("etcdctl --endpoints='$etcd_endpoints' get /services/graphite"));

        // make sure the datasource exists
        create_graphite_datasource_if_missing($grafana_url);

        // make sure the dashboard exists
        $dashboard_json = json_decode( str_replace('{MACHINE_NAME}', $machine_name, file_get_contents(__DIR__.'/dashboards/basic-metrics.json')) );
        create_grafana_dashboard_if_missing($grafana_url, $machine_name, $dashboard_json);
    }
    else
        error_log('/services/graphite is empty');

    // Sleep
    sleep(20);
}
