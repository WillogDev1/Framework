<?php

require __DIR__ . '/../vendor/autoload.php';

use Engine\Http\Http;

$http = new Http();

print_r($http->getRequestMethod());
