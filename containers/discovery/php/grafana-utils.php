<?php

require_once __DIR__.'/HttpRequest.php';

function create_graphite_datasource($grafana_url, $auth = 'admin:admin') {
    $fields = [
        'name'      => 'Graphite',
        'type'      => 'graphite',
        'url'       => 'http://localhost:8000',
        'access'    => 'proxy',
        'basicAuth' => false ];

    HttpRequest::perform('POST', $grafana_url.'/api/datasources', [], $fields, $auth);

}

function get_all_datasources($grafana_url, $auth = 'admin:admin') {
    $r = HttpRequest::perform('GET', $grafana_url.'/api/datasources', [], false, $auth);
    return json_decode($r['body'], true);
}

function create_graphite_datasource_if_missing($grafana_url, $auth = 'admin:admin') {
    $datasources = get_all_datasources($grafana_url, $auth);
    $found = array_filter($datasources, function($d) { return 'Graphite' == $d['name'] && 'graphite' == $d['type']; });
    if(0 == count($found))
        create_graphite_datasource($grafana_url);
}

function create_update_dashboard($grafana_url, $json_obj, $auth = 'admin:admin') {
    $json_obj->id = null;
    $post_fields = [
        'Dashboard' => $json_obj,
        'overwrite' => true
    ];
    //$post_fields = $json_obj;

        print_r(json_encode($post_fields));

    $r = HttpRequest::perform('POST', $grafana_url.'/api/dashboards/db', ['Content-Type: application/json'], json_encode($post_fields), $auth);
    // /api/dashboards/db
    print_r($r);
}