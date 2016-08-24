<?php

require_once __DIR__.'/containers/discovery/php/grafana-utils.php';

create_update_dashboard(
    'http://172.17.8.101/',
    json_decode(file_get_contents(__DIR__.'/containers/graphite/dashboards/apache-1.json')));

