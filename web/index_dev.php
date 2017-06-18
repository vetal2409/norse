<?php

use e1\Application;
use e1\models\role;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

# IF CORS Request
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
    header('Access-Control-Allow-Headers: X-Requested-With, Accept, Content-Type, Authorization, X-Access-Token');
    header('content-type: application/json; charset=utf-8');
}

# IF OPTIONS Request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE');
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

error_reporting(E_ALL);

ini_set("display_errors", 1);

/** @var Application $app */

$app = require __DIR__ . '/app.php';

require __DIR__ . '/../config/dev.php';

$app->register(new Sorien\Provider\PimpleDumpProvider());

$app->run();

