<?php

require_once __DIR__.'/containers/discovery/php/grafana-utils.php';

if (false)
    create_update_dashboard(
        'http://172.17.8.101/',
        json_decode(file_get_contents(__DIR__.'/containers/graphite/dashboards/apache-1.json')));

if (false)
    print_r(HttpRequest::perform('GET', 'http://172.17.8.101/api/dashboards/db/apache-2', [], false, 'admin:admin'));

if (true)
    create_grafana_dashboard_if_missing(
        'http://172.17.8.101', 'apache-2',
        json_decode( str_replace('{MACHINE_NAME}', 'apache-2', file_get_contents(__DIR__.'/containers/discovery/php/dashboards/basic-metrics.json')) )
    );