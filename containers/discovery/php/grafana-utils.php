<?php

require_once __DIR__.'/HttpRequest.php';

function create_graphite_datasource($grafana_url) {
    $fields = [
        'name'      => 'Graphite',
        'type'      => 'graphite',
        'url'       => 'http://localhost:8000',
        'access'    => 'proxy',
        'basicAuth' => false ];

    $r = HttpRequest::perform('POST', $grafana_url.'/api/datasources', [], $fields);
    print_r($r);
}

function get_all_datasources($grafana_url) {
    $r = HttpRequest::perform('GET', $grafana_url.'/api/datasources');
    return json_decode($r['body'], true);
}

function create_graphite_datasource_if_missing($grafana_url) {
    $datasources = get_all_datasources($grafana_url);
    $found = array_filter($datasources, function($d) { return 'Graphite' == d['name'] && 'graphite' == d['type']; });
    if(0 == count($found))
        create_graphite_datasource($grafana_url);
}