<?php
require __DIR__ . '/../vendor/autoload.php';

use KM\Saffron\Request;
use KM\Saffron\Executor;

$router = require __DIR__ . '/router.compiled.php';
$request = Request::createFromGlobals();
$route = $router->dispatch($request);

if ($route) {
    $executor = new Executor($route);
    $executor->fire();
}
else {
    echo 'Error 404';
}
