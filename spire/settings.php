<?php

require_once __DIR__.'/custom_resource.php';

$MONGODB_CONNECTION_URI = "mongodb://localhost/test";
$MONGODB_OPTIONS = array();
# $RESOURCE_CLASS = 'Spire\Resources\Resource';
$RESOURCE_CLASS = 'CustomResource';

