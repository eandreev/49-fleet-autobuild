#!/bin/bash

curl 'http://admin:admin@0.0.0.0:3000/api/datasources' \
    -X POST -H "Content-Type: application/json" \
    --data-binary <<DATASOURCE \
      '{
            "name":"Graphite",
            "type":"graphite",
            "url":"http://localhost:8000",
            "access":"proxy",
            "basicAuth":false
       }'
DATASOURCE
